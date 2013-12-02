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
     
     QSqlQuery query("SELECT * FROM `all` WHERE `ip` not LIKE '91.%' `activ`='y' AND `ip`='"+ip_argv+"' LIMIT 1");
     while (query.next()) {
         QString id = query.value(5).toString();
    
         QString ip = query.value(0).toString();	 
         QString in = query.value(3).toString();	 
         QString out = query.value(4).toString();	 
         QString n = query.value(1).toString();	 	 
	 bw1 =query.value(3).toInt();	 
	 QString bw2 =query.value(3).toString();	 
//pfctl	 
//	exec_this="/sbin/pfctl -tinat -Tadd ";
//	exec_this +=ip;
//	qDebug()<<exec_this<<endl;	

        exec_this ="/sbin/ipfw table 1 add ";
        exec_this +=ip;

        system(exec_this.toStdString().c_str());	


    }
	qDebug()<<exec_this<<endl;	

     QSqlQuery query("SELECT * FROM `all` WHERE `activ`='y' AND `ip`='"+ip_argv+"' LIMIT 1");
     while (query.next()) {
//pfctl	 
//	exec_this="/sbin/pfctl -tinat -Tadd ";
//	exec_this +=ip;
//	qDebug()<<exec_this<<endl;	

         QString id = query.value(5).toString();
    
         QString ip = query.value(0).toString();	 
         QString in = query.value(3).toString();	 
         QString out = query.value(4).toString();	 
         QString n = query.value(1).toString();	 	 
	 bw1 =query.value(3).toInt();	 
	 QString bw2 =query.value(3).toString();	 


    }



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
		    //qDebug()<<"NUM>0|"<<num1<<"|"<<endl;	
	            exec_this ="/sbin/ipfw pipe ";
		    exec_this +=num1;
		    exec_this +=" config mask dst-ip 0x000000ff bw ";
	    	    exec_this +=bw2;
		    exec_this +="Kbit/s";
    		    system(exec_this.toStdString().c_str());		    		
		    //qDebug()<<exec_this<<endl;	

	            exec_this ="/sbin/ipfw table ";
		    exec_this +=num1;
		    exec_this +=" add ";
	    	    exec_this +=ip;
    		    system(exec_this.toStdString().c_str());		    		
		    //qDebug()<<exec_this<<endl;	

	    
	    
	    }
	    else
	    {
		    
		    QSqlQuery query1(" SELECT MAX(`n`)+1  FROM `freenibs`.`pipes` ");
		    
		    //qDebug()<<num<<endl;
		    // qDebug()<<query1.lastError()<<endl;
		    // query.exec();
		     //qDebug()<<query1.lastError()<<endl;
		     while (query1.next()) {
			    //num = query1.value(0).toInt();	 	 
	    		    num1 = query1.value(0).toString();	 	 
			}
	    	    //qDebug()<<"NUM:"<<num1<<endl;	

		     //QSqlQuery 
		     query.prepare("INSERT INTO `freenibs`.`pipes`  ( `bw`,`n`) values( '" +bw2+ "','"+num1+"')");
		    // qDebug()<<query.lastError()<<endl;
		     query.exec();		    		     
	    
		    exec_this ="/sbin/ipfw delete ";
		    exec_this +=num1;
    	            system(exec_this.toStdString().c_str());		    
		    //qDebug()<<exec_this<<endl;	
		    
	            exec_this ="/sbin/ipfw pipe ";
		    exec_this +=num1;
		    exec_this +=" config mask dst-ip 0x000000ff bw ";
	    	    exec_this +=bw2;
		    exec_this +="Kbit/s";
    		    system(exec_this.toStdString().c_str());		    		
		    //qDebug()<<exec_this<<endl;			    

	            exec_this ="/sbin/ipfw -q add ";
		    exec_this +=num1;
		    exec_this +=" pipe ";
	    	    exec_this +=num1;
		    exec_this +=" all from table\\(";
	    	    exec_this +=num1;		    
	    	    exec_this +="\\) to not 10.0.0.0/8 out";		    
    		    system(exec_this.toStdString().c_str());		    		
		    //qDebug()<<exec_this<<endl;	


	            exec_this ="/sbin/ipfw -q add ";
		    exec_this +=num1;
		    exec_this +=" pipe ";
	    	    exec_this +=num1;
		    exec_this +=" all from not 10.0.0.0/8 to table\\(";
	    	    exec_this +=num1;		    
	    	    exec_this +="\\) in ";		    
    		    system(exec_this.toStdString().c_str());		    		
		    //qDebug()<<exec_this<<endl;	
		    

	            exec_this ="/sbin/ipfw table ";
		    exec_this +=num1;
		    exec_this += " add ";
	    	    exec_this +=ip;
    		    system(exec_this.toStdString().c_str());		    		
		    //qDebug()<<exec_this<<endl;	
	    }
	}
	QSqlQuery query("INSERT INTO `freenibs`.`updown` (`ip` ,`time` ,`status`) VALUES ( '"+ip_argv+"',CURRENT_TIMESTAMP , 'up');");
	query.clear();
       
     db.close();  
//    std::cout<<"HELLO";
//    return a.exec();
}

