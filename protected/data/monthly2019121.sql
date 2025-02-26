insert into opr_monthly_field(code, name, upd_type, field_type, status, function_name, lcu, luu)
values ('100085','减：蔚诺空气外包服务','M','N','Y','14','admin','admin');

insert into opr_monthly_dtl(hdr_id, data_field, manual_input, lcu, luu) 
select id, '100085','N','admin','admin' from opr_monthly_hdr where year_no=2019 and month_no=12;