INSERT INTO ezcontentclass_attribute
( can_translate, category, contentclass_id, data_float1, data_float2, data_float3, data_float4, data_int1, data_int2, data_int3, data_int4, data_text1, data_text2, data_text3, data_text4, data_text5, data_type_string, identifier, is_information_collector, is_required, is_searchable, placement, serialized_data_text, serialized_description_list, serialized_name_list, version )
VALUES( 1, '', 16, 0, 0, 0, 0, 0, 0, 0, 0, '', '', '', '', '', 'ezscip', 'ip', 0, 0, 0, 16, 'a:0:{}', 'a:1:{s:6:"eng-GB";s:0:"";}', 'a:2:{s:6:"eng-GB";s:2:"IP";s:16:"always-available";s:6:"eng-GB";}', 0 );

INSERT INTO ezcontentobject_attribute
( attribute_original_id, contentclassattribute_id, contentobject_id, data_float, data_int, data_text, data_type_string, language_code, language_id, sort_key_int, sort_key_string, version )
SELECT 0, (SELECT id FROM ezcontentclass_attribute WHERE data_type_string = 'ezscip'), id, 0, 0, '', 'ezscip', 'eng-GB', 2, 0, '', 1
FROM ezcontentobject WHERE contentclass_id = 16;



UPDATE ezcontentobject_attribute
SET data_text = '127.0.0.1'
WHERE data_type_string = 'ezscip' AND contentobject_id = 71;



DELETE FROM ezcontentclass_attribute WHERE data_type_string = 'ezscip';
DELETE FROM ezcontentobject_attribute WHERE data_type_string = 'ezscip';
