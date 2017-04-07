use library;
 create  table book
   (bno char(11), 
   category 	varchar(10),
   title 	varchar(20),
   press	varchar(20),
   year int,
   author varchar(10),
   price	decimal(7,2),
   total	int,
   stock	int,
   primary key(bno));

  create table card
  (cno char(7),
  name varchar(10),
  department varchar(40),
  type char(1),
  primary key(cno),
  check(type in('T','S')));

  create table borrow
  (cno char(7),
  bno  char(11),
  borrow_date datetime,
  return_date datetime,
  due_date datetime,
  done tinyint(1) DEFAULT 0,
  primary key(cno,bno,borrow_date),
  foreign key (cno) references card(cno) on delete cascade,
  foreign key (bno) references book(bno) ) ;


