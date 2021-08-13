alter table opr_monthly_hdr add column group_id char(1) not null default '1' after status;
alter table opr_monthly_field add column group_id char(1) not null default '1' after function_name;
insert into opr_monthly_field(code, name, upd_type, field_type, status, function_name, group_id, lcu, luu)
values ('20001','ID-空气服务收入','M','N','Y','1','2','admin','admin'),
('20002','ID-机器售卖收入','M','N','Y','2','2','admin','admin'),
('20003','ID-延长维保收入','M','N','Y','3','2','admin','admin'),
('20004','ID收入合计','Y','N','Y','4','2','admin','admin')
;

use workflowuat
insert into wf_process (code, name) values ('OPRPT2', 'Operation Report (ID) Process');
insert into wf_process_version (process_id, start_dt, end_dt) values (4, '2021-01-01', '2099-12-31');
call copyworkflow(3, 6);