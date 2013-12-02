#include <QtCore>
#include <QCoreApplication>
#include <QtSql>
#include <QFile>
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
#define DBNAME "freenibs"
#define USER "arpwatch"
#define PASSWORD "RLMXXGpCeNw8uDva"

int main(int argc, char *argv[])
{
    QCoreApplication a(argc, argv);
    QTextCodec *codec = QTextCodec::codecForName("CP1251");
    QTextCodec::setCodecForTr(codec);
    QTextCodec::setCodecForCStrings(codec);
    QSqlDatabase db = QSqlDatabase::addDatabase(SQLDRIVER);
    if( !db.isDriverAvailable(SQLDRIVER))
    {
	exit(1);
    }

    db.setHostName(HOST);
    db.setDatabaseName(DBNAME);
    db.setUserName(USER);
    db.setPassword(PASSWORD);
    //db.setPort(3306);
    QFile file("/arpwatch.table");
    if (! file.exists() ){
	std::cout<<" /arpwatch.table 1";
	db.close();  
	exit(1);
    }
    if( !file.open(QIODevice::ReadOnly)){
    	std::cout<<" /arpwatch.table 2";
	db.close();  
	exit(1);
    }
    bool ok=db.open();
    if(ok!=true){
    	std::cout<<"unable connec't";
    	exit(1);
    }

     QString ip_argv,mac,user;
     ip_argv=argv[1];
     mac=argv[2];
     
     //qDebug()<<ip_argv;
     int z=0;
     while (!file.atEnd()) {
         QString line = file.readLine();
         z=0;
         mac="";
         ip_argv="";
         user="";
         
         
	for(int i=0;i<line.size();++i){
	    if(z==1)
		z=2;

	    if(line.at(i)=='K')
		z=1;
	    if(line.at(i)=='L')
		z=3;

	    if(z==0)
		ip_argv.append(line.at(i));
	    

	    if(z==2)
		mac.append(line.at(i));
	}
	
     QSqlQuery query("SELECT `user` FROM `actions` WHERE `call_from` = '"+ip_argv+"' AND `start_time` > FROM_UNIXTIME( UNIX_TIMESTAMP( NOW( ) ) -604800 ) GROUP BY `user` LIMIT 1");
     while (query.next()) {
        /* QString id = query.value(5).toString();
         QString ip = query.value(0).toString();	 
         QString in = query.value(3).toString();	 
         QString out = query.value(4).toString();	 
         QString n = query.value(1).toString();	 	 
	 bw1 =query.value(3).toInt();	 
	 QString bw2 =query.value(3).toString();	 
	 
	*/
	user = query.value(0).toString();
	//qDebug()<<ip_argv<<" ---> "<<user;
	//qDebug()<<ip_argv<<" ---> "<<user<<"--->"<<mac<<endl;
	//qDebug()<<"SELECT `user` FROM `actions` WHERE `call_from` = '"+ip_argv+"' AND `start_time` > FROM_UNIXTIME( UNIX_TIMESTAMP( NOW( ) ) -604800 ) GROUP BY `user`";
	query.clear();
	query.prepare("UPDATE `freenibs`.`users` SET `freenibs`.`users`.`mac_addr` = '"+mac+"' WHERE `freenibs`.`users`.`user` ='"+user+"' LIMIT 1");
		//QSqlQuery query1("UPDATE `freenibs`.`users` SET `freenibs`.`users`.`mac_addr` = '"+mac+"' WHERE `freenibs`.`users`.`user` ='"+user+"' LIMIT 1");
	query.exec();
		//query1.clear();
	}
	
	
	
	}
	file.close();
	
	//dhcpd



	 QFile file2("/usr/local/etc/dhcpd.conf_head");
        if (! file2.exists() ){
        	std::cout<<" dhcpd_head 1";
    	    db.close();  
		exit(1);
	}
	if( !file2.open(QIODevice::ReadOnly)){
	    std::cout<<" dhcpd 2";
	    db.close();  
		exit(1);
	}
	
	QFile file1("/usr/local/etc/dhcpd.conf");
        if (! file1.exists() ){
    	    std::cout<<" dhcpd 1";
    	    db.close();  
		exit(1);
		
	}
	//file1.remove;
	/* QFile::remove(file1);
	bool c =file2.copy(file1);
	 if(!f)
	    return;
	*/
	
	    
	 if( !file1.open(QIODevice::WriteOnly | QIODevice::Truncate | QIODevice::Text)){
	    std::cout<<" dhcpd 2";
	    db.close();  
		exit(1);
	}
	
	QTextStream out(&file1);

	 while (!file2.atEnd()){
	    QString line=file2.readLine();
	    out<<line;
	}
	

	QString ip;
	user="";
	mac="";
	 QSqlQuery query4("SELECT `user`,`local_addr`,`mac_addr` FROM `users` WHERE `mac_addr` LIKE '%:%' AND `local_addr` LIKE '%.%'  GROUP BY `mac_addr`");
	//query.exec();
	while (query4.next()) {
	    user = query4.value(0).toString();
	    ip = query4.value(1).toString();
	    mac = query4.value(2).toString();
	    out <<"  host "<<user<<" {"<<endl;
	    out <<"  hardware ethernet "<<mac<<";"<<endl;
	    out <<"  fixed-address "<<ip<<";"<<endl;

	    out <<"  option domain-name-servers 10.200.25.1,10.200.25.2, 10.200.24.7;"<<endl;
	    out <<"  option broadcast-address 10.200.31.255;"<<endl;
	    out <<"  option ms-classless-static-routes 8, 10, 10,200,25,7, 18, 192,168,0,  10,200,25,7, 29, 91,203,143,0, 10,200,25,7;"<<endl;
	    out <<"  option rfc3442-classless-static-routes 8, 10, 10,200,25,7, 18, 192,168,0,  10,200,25,7, 29, 91,203,143,0, 10,200,25,7;"<<endl;


    
	    out <<"} "<<endl;
	    //qDebug()<<ip<<" ---> "<<user<<"--->"<<mac<<endl;
	}
	//qDebug()<<query4.lastError();
	file1.close();	
	file2.close();	
        db.close();  
}

