require_relative 'converter'

class EADConverter < Converter

  require 'securerandom'
  require_relative 'lib/xml_sax'
  include ASpaceImport::XML::SAX


  def self.import_types(show_hidden = false)
    [
     {
       :name => "ead_xml",
       :description => "Import EAD records from an XML file"
     }
    ]
  end


  def self.instance_for(type, input_file)
    if type == "ead_xml"
      self.new(input_file)
    else
      nil
    end
  end


  def self.profile
    "Convert EAD To ArchivesSpace JSONModel records"
  end

  # We override this to skip nodes that are often very deep
  # We can safely assume ead, c, and archdesc will have children,
  # which greatly helps the performance. 
  def is_node_empty?(node)
    parent_nodes = %w{ ead e archdesc dsc } + (1..12).collect { |n| "c#{ sprintf('%02d', n)}" }  
    if  parent_nodes.include?( node.local_name ) 
      return false 
    else
      return  node.inner_xml.strip.empty? 
    end
  end

  def self.configure

    with 'ead' do |node|
      make :resource
    end


    with 'archdesc' do
      set :level, att('level') || 'otherlevel'
      set :other_level, att('otherlevel')
      set :publish, att('audience') != 'internal'
    end


    # c, c1, c2, etc...
    (0..12).to_a.map {|i| "c" + (i+100).to_s[1..-1]}.push('c').each do |c|
      with c do
        make :archival_object, {
          :level => att('level') || 'otherlevel',
          :other_level => att('otherlevel'),
          :ref_id => att('id'),
          :resource => ancestor(:resource),
          :parent => ancestor(:archival_object),
          :publish => att('audience') != 'internal'
        }
      end
    end


    with 'unitid' do |node|
      ancestor(:note_multipart, :resource, :archival_object) do |obj|
        case obj.class.record_type
        when 'resource'
          # inner_xml.split(/[\/_\-\.\s]/).each_with_index do |id, i|
          #   set receiver, "id_#{i}".to_sym, id
          # end
          set obj, :id_0, inner_xml
        when 'archival_object'
          set obj, :component_id, inner_xml.gsub(/[\/_\-.]/, '_')
        end
      end
    end


    with 'unittitle' do |node|
      ancestor(:note_multipart, :resource, :archival_object) do |obj|
        klass =  obj.class.record_type
        obj.title = node.inner_xml.strip.gsub(/<[^>]*>/, ' ') unless klass == "note_multipart"
      end
    end


    with 'unitdate' do |node|

      norm_dates = (att('normal') || "").sub(/^\s/, '').sub(/\s$/, '').split('/')
      if norm_dates.length == 1
        norm_dates[1] = norm_dates[0]
      end
      norm_dates.map! {|d| d =~ /^([0-9]{4}(\-(1[0-2]|0[1-9])(\-(0[1-9]|[12][0-9]|3[01]))?)?)$/ ? d : nil}
      
      make :date, {
        :date_type => att('type') || 'inclusive', 
        :expression => inner_xml,
        :label => 'creation',
        :begin => norm_dates[0],
        :end => norm_dates[1],
        :calendar => att('calendar'),
        :era => att('era'),
        :certainty => att('certainty')
      } do |date|
        set ancestor(:resource, :archival_object), :dates, date
      end
    end


    with 'language' do |node|
      set ancestor(:resource, :archival_object), :language, att('langcode')
    end


    with 'physdesc' do
      physdesc = Nokogiri::XML::DocumentFragment.parse(inner_xml)
      extent_number_and_type = nil
      other_extent_data = []
      make_note_too = false
      physdesc.children.each do |child|
        if child.respond_to?(:name) && child.name == 'extent'
          child_content = child.content.strip 
          if extent_number_and_type.nil? && child_content =~ /^([0-9\.]+)+\s+(.*)$/
            extent_number_and_type = {:number => $1, :extent_type => $2}
          else
            other_extent_data << child_content
          end
        else
          # there's other info here; make a note as well
          make_note_too = true unless child.text.strip.empty?
        end
      end

      # only make an extent if we got a number and type
      if extent_number_and_type
        make :extent, {
          :number => $1,
          :extent_type => $2,
          :portion => 'whole',
          :container_summary => other_extent_data.join('; ')
        } do |extent|
          set ancestor(:resource, :archival_object), :extents, extent
        end
      else
        make_note_too = true;
      end

      if make_note_too
        content = physdesc.to_xml(:encoding => 'utf-8')
        make :note_singlepart, {
          :type => 'physdesc',
          :persistent_id => att('id'),
          :content => content.sub(/<head>.*?<\/head>/, '').strip
        } do |note|
          set ancestor(:resource, :archival_object), :notes, note
        end
      end

    end


    with 'bibliography' do
      make :note_bibliography
      set :persistent_id, att('id')
      set ancestor(:resource, :archival_object), :notes, proxy
    end


    with 'index' do
      make :note_index
      set :persistent_id, att('id')
      set ancestor(:resource, :archival_object), :notes, proxy
    end


    %w(bibliography index).each do |x|
      with "#{x}/head" do |node|
        set :label, inner_xml
      end

      with "#{x}/p" do
        set :content, inner_xml
      end
    end


    with 'bibliography/bibref' do
      set :items, inner_xml
    end


    {
      'name' => 'name',
      'persname' => 'person',
      'famname' => 'family',
      'corpname' => 'corporate_entity',
      'subject' => 'subject',
      'function' => 'function',
      'occupation' => 'occupation',
      'genreform' => 'genre_form',
      'title' => 'title',
      'geogname' => 'geographic_name'
    }.each do |k, v|
      with "indexentry/#{k}" do |node|
        make :note_index_item, {
          :type => v,
          :value => inner_xml
        } do |item|
          set ancestor(:note_index), :items, item
        end
      end
    end

    # this is very imperfect. 
    with 'indexentry/ref' do
        make :note_index_item, {
          :type => 'name',
          :value => inner_xml,
          :reference_text => inner_xml,
          :reference =>  att('target')
        } do |item|
          set ancestor(:note_index), :items, item
        end
    end


    %w(accessrestrict accessrestrict/legalstatus \
       accruals acqinfo altformavail appraisal arrangement \
       bioghist custodhist dimensions \
       fileplan odd otherfindaid originalsloc phystech \
       prefercite processinfo relatedmaterial scopecontent \
       separatedmaterial userestrict).each do |note|
      with note do |node|
        content = inner_xml.tap {|xml|
          xml.sub!(/<head>.*?<\/head>/m, '')
          # xml.sub!(/<list [^>]*>.*?<\/list>/m, '')
          # xml.sub!(/<chronlist [^>]*>.*<\/chronlist>/m, '')
        }

        make :note_multipart, {
          :type => node.name,
          :persistent_id => att('id'),
          :subnotes => {
            'jsonmodel_type' => 'note_text',
            'content' => content.strip
          }
        } do |note|
          set ancestor(:resource, :archival_object), :notes, note
        end
      end      
    end


    %w(abstract langmaterial materialspec physfacet physloc).each do |note|
      with note do |node|
        content = inner_xml
        next if content =~ /\A<language langcode=\"[a-z]+\"\/>\Z/

        if content.match(/\A<language langcode=\"[a-z]+\"\s*>([^<]+)<\/language>\Z/)
          content = $1
        end

        make :note_singlepart, {
          :type => note,
          :persistent_id => att('id'),
          :content => content.sub(/<head>.*?<\/head>/, '').strip
        } do |note|
          set ancestor(:resource, :archival_object), :notes, note
        end
      end
    end


    with 'notestmt/note' do
      append :finding_aid_note, inner_xml
    end


    with 'chronlist' do
      make :note_chronology do |note|
        set ancestor(:note_multipart), :subnotes, note
      end
    end


    with 'chronitem' do
      context_obj.items << {}
    end


    %w(eventgrp/event chronitem/event).each do |path|
      with path do
        context_obj.items.last['events'] ||= []
        context_obj.items.last['events'] << inner_xml
      end
    end


    with 'list' do
      type = att('type')
      if type == 'deflist' || (type.nil? && inner_xml.match(/<deflist>/))
        make :note_definedlist do |note|
          set ancestor(:note_multipart), :subnotes, note
        end
      else
        make :note_orderedlist, {
          :enumeration => att('numeration')
        } do |note|
          set ancestor(:note_multipart), :subnotes, note
        end
      end
    end


    with 'list/head' do |node|
      set :title, inner_xml
    end


    with 'defitem' do |node|
      context_obj.items << {}
    end

    with 'defitem/label' do |node|
      context_obj.items.last['label'] = inner_xml if context == :note_definedlist
    end


    with 'defitem/item' do |node|
      context_obj.items.last['value'] =  inner_xml if context == :note_definedlist
    end


    with 'list/item' do
      set :items, inner_xml if context == :note_orderedlist
    end


    with 'publicationstmt/date' do
      set :finding_aid_date, inner_xml if context == :resource
    end


    with 'date' do
      if context == :note_chronology
        date = inner_xml
        context_obj.items.last['event_date'] = date
      end
    end


    with 'head' do
      if context == :note_multipart
        set :label, inner_xml
      elsif context == :note_chronology
        set :title, inner_xml
      end
    end


    # example of a 1:many tag:record relation (1+ <container> => 1 instance with 1 container)
    with 'container' do

      if context_obj.instances.empty?
        make :instance, {
          :instance_type => 'mixed_materials'
        } do |instance|
          set ancestor(:resource, :archival_object), :instances, instance
        end
      end

      inst = context == :instance ? context_obj : context_obj.instances.last

      if inst.container.nil?
        make :container do |cont|
          set inst, :container, cont
        end
      end

      cont = inst.container || context_obj

      (1..3).to_a.each do |i|
        next unless cont["type_#{i}"].nil?
        cont["type_#{i}"] = att('type')
        cont["indicator_#{i}"] = inner_xml
        break
      end
    end


    with 'author' do
      set :finding_aid_author, inner_xml
    end


    with 'descrules' do
      set :finding_aid_description_rules, inner_xml
    end


    with 'eadid' do
      set :ead_id, inner_xml
      set :ead_location, att('url')
    end


    with 'editionstmt' do
      set :finding_aid_edition_statement, inner_xml
    end


    with 'seriesstmt' do
      set :finding_aid_series_statement, inner_xml
    end


    with 'sponsor' do
      set :finding_aid_sponsor, inner_xml
    end


    with 'titleproper' do
      type = att('type')
      case type
      when 'filing'
        set :finding_aid_filing_title, inner_xml
      else
        set :finding_aid_title, inner_xml
      end
    end


    with 'langusage' do
      set :finding_aid_language, inner_xml
    end


    with 'revisiondesc' do
      set :finding_aid_revision_description, inner_xml
    end


    with 'origination/corpname' do
      make_corp_template(:role => 'creator')
    end


    with 'controlaccess/corpname' do
      make_corp_template(:role => 'subject')
    end


    with 'origination/famname' do
      make_family_template(:role => 'creator')
    end


    with 'controlaccess/famname' do
      make_family_template(:role => 'subject')
    end


    with 'origination/persname' do
      make_person_template(:role => 'creator')
    end


    with 'controlaccess/persname' do
      make_person_template(:role => 'subject')
    end


    {
      'function' => 'function',
      'genreform' => 'genre_form',
      'geogname' => 'geographic',
      'occupation' => 'occupation',
      'subject' => 'topical'
      }.each do |tag, type|
        with "controlaccess/#{tag}" do
          make :subject, {
            :terms => {'term' => inner_xml, 'term_type' => type, 'vocabulary' => '/vocabularies/1'},
            :vocabulary => '/vocabularies/1',
            :source => att('source') || 'ingest'
          } do |subject|
            set ancestor(:resource, :archival_object), :subjects, {'ref' => subject.uri}
          end
        end
     end


    with 'dao' do
      make :instance, {
          :instance_type => 'digital_object'
        } do |instance|
          set ancestor(:resource, :archival_object), :instances, instance
      end

      
      make :digital_object, {
        :digital_object_id => SecureRandom.uuid,
        :title => att('title'),
       } do |obj|
         obj.file_versions <<  {   
             :use_statement => att('role'),
             :file_uri => att('href'),
             :xlink_actuate_attribute => att('actuate'),
             :xlink_show_attribute => att('show')
         }
         set ancestor(:instance), :digital_object, obj 
      end

    end
    
    with 'daodesc' do
        make :note_digital_object, {
          :type => node.name,
          :persistent_id => att('id'),
          :content => inner_xml.strip
        } do |note|
          set ancestor(:digital_object), :notes, note
        end
    end
    
    with 'daogrp' do
      title = '' 
      ancestor(:resource, :archival_object ) { |ao| title << ao.title + ' Digital Object' } 
      
      make :digital_object, {
        :digital_object_id => SecureRandom.uuid,
        :title => title,
       } do |obj|
         ancestor(:resource, :archival_object) do |ao|
          ao.instances.push({'instance_type' => 'digital_object', 'digital_object' => {'ref' => obj.uri}})
        end
      end

    end
  
  
   with 'daoloc' do
     ancestor(:digital_object) do |dobj|
      dobj.file_versions << {
       :file_uri => att('href'),
       :xlink_show_attribute => att('show'),
       :file_format_name => att('role')  
       } 
     end
   end
  
  end
  # Templates Section

  def make_corp_template(opts)
    make :agent_corporate_entity, {
      :agent_type => 'agent_corporate_entity'
    } do |corp|
      set ancestor(:resource, :archival_object), :linked_agents, {'ref' => corp.uri, 'role' => opts[:role]}
    end

    make :name_corporate_entity, {
      :primary_name => inner_xml,
      :rules => att('rules'),
      :source => att('source') || 'ingest'
    } do |name|
      set ancestor(:agent_corporate_entity), :names, proxy
    end
  end


  def make_family_template(opts)
    make :agent_family, {
      :agent_type => 'agent_family',
    } do |family|
      set ancestor(:resource, :archival_object), :linked_agents, {'ref' => family.uri, 'role' => opts[:role]}
    end

    make :name_family, {
      :family_name => inner_xml,
      :rules => att('rules'),
      :source => att('source') || 'ingest'
    } do |name|
      set ancestor(:agent_family), :names, name
    end
  end


  def make_person_template(opts)
    make :agent_person, {
      :agent_type => 'agent_person',
    } do |person|
      set ancestor(:resource, :archival_object), :linked_agents, {'ref' => person.uri, 'role' => opts[:role]}
    end

    make :name_person, {
      :name_order => 'inverted',
      :primary_name => inner_xml,
      :rules => att('rules'),
      :source => att('source') || 'ingest'
    } do |name|
      set ancestor(:agent_person), :names, name
    end
  end
end