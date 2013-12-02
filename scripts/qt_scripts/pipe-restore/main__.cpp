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
//     int bw1;
    
     QString ip_argv;
     ip_argv=argv[4];
     
     //qDebug()<<ip_argv;
     
	    qint64 num = 0;
	    QString num1;

	QSqlQuery query("SELECT * FROM `freenibs`.`pipes` ");

	while (query.next()) 
	{
		    num = query.value(1).toInt();	 	 
		    num1 = query.value(1).toString();	 	 
		    QString bw2 = query.value(2).toString();	 	 
		
	    //query.clear();
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
		    exec_this +=" all from not 10.0.0.0/8 to table\\(";
	    	    exec_this +=num1;		    
	    	    exec_this +="\\) in ";		    
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

		}
	    }
       
     db.close();  
//    std::cout<<"HELLO";
//    return a.exec();
}

