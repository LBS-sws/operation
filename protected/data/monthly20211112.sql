insert into opr_monthly_field(code, name, upd_type, field_type, status, function_name,group_id, lcu, luu)
values ('100055','隔油池收入','M','N','Y','16',1,'admin','admin');

insert into opr_monthly_dtl(hdr_id, data_field, manual_input, lcu, luu) 
select id, '100055','N','admin','admin' from opr_monthly_hdr where year_no=2021 and month_no=11;