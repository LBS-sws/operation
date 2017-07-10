CREATE DATABASE workflowdev CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON workflowdev.* TO 'swuser'@'localhost' IDENTIFIED BY 'swisher168';

use workflowdev;

DROP TABLE IF EXISTS wf_process;
CREATE TABLE wf_process(
	id int unsigned not null auto_increment primary key,
	code varchar(15) not null,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_process_version;
CREATE TABLE wf_process_version(
	id int unsigned not null auto_increment primary key,
	process_id int unsigned not null,
	start_dt datetime not null,
	end_dt datetime not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_action;
CREATE TABLE wf_action(
	id int unsigned not null auto_increment primary key,
	proc_ver_id int unsigned not null,
	code varchar(15) not null,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS wf_task;
CREATE TABLE wf_task(
	id int unsigned not null auto_increment primary key,
	proc_ver_id int unsigned not null,
	name varchar(255) not null,
	function_call varchar(255) not null,
	param varchar(1000),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS wf_action_task;
CREATE TABLE wf_action_task(
	action_id int unsigned not null,
	task_id int unsigned not null,
	seq_no int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_state;
CREATE TABLE wf_state(
	id int unsigned not null auto_increment primary key,
	proc_ver_id int unsigned not null,
	code char(2) not null,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_transition;
CREATE TABLE wf_transition(
	id int unsigned not null auto_increment primary key,
	proc_ver_id int unsigned not null,
	current_state int unsigned not null,
	next_state int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request;
CREATE TABLE wf_request(
	id int unsigned not null auto_increment primary key,
	proc_ver_id int unsigned not null,
	current_state int unsigned not null,
	doc_id int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request_data;
CREATE TABLE wf_request_data(
	id int unsigned not null auto_increment primary key,
	request_id int unsigned not null,
	data_name varchar(100) not null,
	data_value varchar(5000),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	UNIQUE KEY request (request_id, data_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request_transit_log;
CREATE TABLE wf_request_transit_log(
	id int unsigned not null auto_increment primary key,
	request_id int unsigned not null,
	old_state int unsigned not null,
	new_state int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request_resp_user;
CREATE TABLE wf_request_resp_user(
	id int unsigned not null auto_increment primary key,
	request_id int unsigned not null,
	log_id int unsigned not null,
	current_state int unsigned not null,
	username varchar(30) not null,
	status char(1) not null default 'P',
	action_id int unsigned default 0,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER //
DROP FUNCTION IF EXISTS RequestStatus //
CREATE FUNCTION RequestStatus(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime) RETURNS char(2)
BEGIN
	DECLARE status char(2);
	SET status = (
		SELECT d.code
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d 
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and a.current_state = d.id
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		LIMIT 1
	);
	RETURN status;
END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS RequestStatusEx //
CREATE FUNCTION RequestStatusEx(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime) RETURNS char(2)
BEGIN
	DECLARE status char(2);
	
	SET status = (
		SELECT IF(d.code<>'ED',d.code,f.code) as status
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d, wf_request_transit_log e, wf_state f 
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and a.current_state = d.id
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		and a.id = e.request_id
		and a.current_state = e.new_state
		and e.old_state = f.id
		order by e.id desc 
		LIMIT 1
	);
	RETURN status;
END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS RequestStatusDesc //
CREATE FUNCTION RequestStatusDesc(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime) RETURNS char(255)
BEGIN
	DECLARE status_desc char(255);
	
	SET status_desc = (
		SELECT IF(d.code<>'ED',d.name,CONCAT(f.name,' (',d.name,')')) as status_desc
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d, wf_request_transit_log e, wf_state f 
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and a.current_state = d.id
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		and a.id = e.request_id
		and a.current_state = e.new_state
		and e.old_state = f.id
		order by e.id desc 
		LIMIT 1
	);
	RETURN status_desc;
END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS RequestStatusDate //
CREATE FUNCTION RequestStatusDate(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime, p_code char(2)) RETURNS datetime
BEGIN
	DECLARE status_dt datetime;
	SET status_dt = (
		SELECT e.lcd
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d, wf_request_transit_log e
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and e.new_state = d.id
		and d.code = p_code 
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		and a.id = e.request_id
		order by e.id desc 
		LIMIT 1
	);
	RETURN status_dt;
END //
DELIMITER ;

DELIMITER //
DROP FUNCTION IF EXISTS ActionPerson //
CREATE FUNCTION ActionPerson(p_proc_code varchar(15), p_doc_id int unsigned, p_req_dt datetime, p_code char(2)) RETURNS varchar(30)
BEGIN
	DECLARE action_user varchar(30);
	SET action_user = (
		SELECT e.username
		FROM wf_request a, wf_process b, wf_process_version c, wf_state d, wf_request_resp_user e
		WHERE a.proc_ver_id = c.id
		and b.id = c.process_id
		and e.current_state = d.id
		and d.code = p_code 
		and b.code = p_proc_code
		and c.start_dt <= p_req_dt
		and c.end_dt >= p_req_dt
		and a.doc_id = p_doc_id
		and a.id = e.request_id
		and e.status = 'C'
		order by e.id desc 
		LIMIT 1
	);
	RETURN action_user;
END //
DELIMITER ;

INSERT INTO wf_process(id, code, name) values(2, 'OPRPT', 'Operation Report Process');

INSERT INTO wf_process_version(id, process_id, start_dt, end_dt) values(3, 2, '2016-01-01', '2099-12-31');

INSERT INTO wf_action(id, proc_ver_id, code, name) values
(101,3,'APPROVE','批准报表'),
(102,3,'DENY','拒绝报表'),
(103,3,'SUBMIT','提交报表'),
(104,3,'RESUBMIT','再提交报表')
;

INSERT INTO wf_task(id, proc_ver_id, name, function_call, param) values
(101,3,'Send Email','sendEmail',''),
(102,3,'Status=Pending for Approval','transit','PA'),
(103,3,'Status=Approved','transit','A'),
(104,3,'Status=Denied','transit','D'),
(105,3,'Route to Approver','routeToApprover',''),
(106,3,'Route to Requestor','routeToRequestor',''),
(107,3,'Status=End','transit','ED'),
(109,3,'Status=Pending for Resubmit','transit','PS'),
(108,3,'Clear All Pending','clearAllPending','')
;

INSERT INTO wf_action_task(action_id, task_id, seq_no) values
(101,103,1),
(101,108,2),
(101,107,3),
(102,104,1),
(102,108,2),
(102,109,3),
(102,106,5),
(103,102,1),
(103,105,2),
(104,108,1),
(104,102,2),
(104,105,3)
;

INSERT INTO wf_state(id, proc_ver_id, code, name) VALUES
(101,3,'ST','开始'),
(102,3,'ED','结束'),
(103,3,'PA','有待审核'),
(104,3,'PS','有待再提交'),
(105,3,'A','已批准'),
(106,3,'D','已拒绝')
;

INSERT INTO wf_transition(proc_ver_id, current_state, next_state) VALUES
(3,101,103),
(3,103,105),
(3,103,106),
(3,105,102),
(3,106,104),
(3,104,103)
;

DROP TABLE IF EXISTS wf_activity;
CREATE TABLE wf_activity(
	id int unsigned not null auto_increment primary key,
	activity_type_id int unsigned not null,
	process_id int unsigned not null,
	name varchar(255),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_target;
CREATE TABLE wf_target(
	id int unsigned not null auto_increment primary key,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO wf_target(name) VALUES
('Requester'),
('Stakeholders'),
('Group Members'),
('Process Admins')
;

DROP TABLE IF EXISTS wf_activity_target;
CREATE TABLE wf_activity_target(
	id int unsigned not null auto_increment primary key,
	activity_type_id int unsigned not null,
	activity_id int unsigned not null,
	target_id int unsigned not null,
	group_id int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request_action;
CREATE TABLE wf_request_action(
	id int unsigned not null auto_increment primary key,
	request_id int unsigned not null,
	action_id int unsigned not null,
	transition_id int unsigned not null,
	is_active tinyint unsigned not null,
	is_complete tinyint unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
