#include <QtCore>
#include <QCoreApplication>
#include <QtSql>
#include <iostream>
#include <cstdlib>
#include <iomanip>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
using namespace std;


#define SQLDRIVER "QMYSQL"
#define HOST "10.200.205.1"
#define DBNAME "bezlim"
#define USER "newbill"
#define PASSWORD "Yb9U7bb9hRRzzyyf"

int main(int argc, char *argv[])
{
    QCoreApplication a(argc, argv);

    QTextCodec *codec = QTextCodec::codecForName("CP1251");
    QTextCodec::setCodecForTr(codec);
    QTextCodec::setCodecForCStrings(codec);

    QSqlDatabase db = QSqlDatabase::addDatabase(SQLDRIVER);

    if( !db.isDriverAvailable(SQLDRIVER))
    {

    }


    db.setHostName(HOST);
    db.setDatabaseName(DBNAME);
    db.setUserName(USER);
    db.setPassword(PASSWORD);
    //db.setPort(3306);
    bool ok=db.open();
    if(ok!=true){
    	std::cout<<"unable connec't";
    	exit(1);
    }
     QString exec_this;
     int bw1;
    
     QString ip_argv;
     ip_argv=argv[4];
     
     //qDebug()<<ip_argv;
     
     QSqlQuery query("SELECT * FROM `all` WHERE `activ`='y' AND `ip`='"+ip_argv+"'");
     while (query.next()) {
         QString id = query.value(5).toString();
         QString ip = query.value(0).toString();	 
         QString in = query.value(3).toString();	 
         QString out = query.value(4).toString();	 
         QString n = query.value(1).toString();	 	 
	 bw1 =query.value(3).toInt();	 
	 QString bw2 =query.value(3).toString();	 
//pf	 
//	exec_this="/sbin/pfctl -tinat -Tdel ";
//	exec_this +=ip;
//	qDebug()<<exec_this<<endl;	
            exec_this ="/sbin/ipfw table 1 delete ";
    	    exec_this +=ip;


        system(exec_this.toStdString().c_str());	

	if(bw1>0)
	{
	    qint64 num = 0;
	    QString num1;
	    query.clear();
	     QSqlQuery query("SELECT * FROM `freenibs`.`pipes` WHERE `freenibs`.`pipes`.`bw`='"+bw2+"' LIMIT 1");
	    //qDebug()<<"SELECT `freenibs`.`pipes`.`n` FROM `freenibs`.`pipes` WHERE `freenibs`.`pipes`.`bw`='"+bw2+"' LIMIT 1"<<endl;		     
	     while (query.next()) {
		    num = query.value(1).toInt();	 	 
		    num1 = query.value(1).toString();	 	 
		}
	    query.clear();
	    if(num>0)
	    {

	            exec_this ="/sbin/ipfw table ";
		    exec_this +=num1;
		    exec_this +=" delete ";
	    	    exec_this +=ip;
    		    system(exec_this.toStdString().c_str());		    		
		    //qDebug()<<exec_this<<endl;	

	    
	    
	    }
	}
	QSqlQuery query("INSERT INTO `freenibs`.`updown` (`ip` ,`time` ,`status`) VALUES ( '"+ip+"',CURRENT_TIMESTAMP , 'down');");
	query.clear();
     }  
     db.close();  
//    std::cout<<"HELLO";
//    return a.exec();
}

