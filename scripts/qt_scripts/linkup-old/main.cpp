#include <QtCore>
#include <QCoreApplication>
#include <QtSql>
#include <iostream>
#include <iomanip>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
using namespace std;


#define SQLDRIVER "QMYSQL"
#define HOST "10.200.25.1"
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
     int bw2;
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

        exec_this="/sbin/pfctl -tinat -Tadd ";
        exec_this +=ip;
//      qDebug()<<exec_this<<endl;
        system(exec_this.toStdString().c_str());
            exec_this ="/sbin/ipfw delete ";
            exec_this += n;
            system(exec_this.toStdString().c_str());



        if(bw1>0)
        {



            exec_this ="/sbin/ipfw pipe ";
            exec_this += n;
            exec_this += " config bw ";
            exec_this +=in;
            exec_this +="Kb/s";
            system(exec_this.toStdString().c_str());
            //qDebug()<<exec_this<<endl;

            exec_this ="/sbin/ipfw add ";
            exec_this += n;
            exec_this += " pipe ";
            exec_this +=n;
            exec_this +=" all from not 10.0.0.0/8 to ";
            exec_this += ip;
            exec_this +=" in";
            system(exec_this.toStdString().c_str());
            //qDebug()<<exec_this<<endl;

            exec_this ="/sbin/ipfw add ";
            exec_this += n;
            exec_this += " pipe ";
            exec_this +=n;
            exec_this +=" all from ";
            exec_this += ip;
            exec_this +=" to not 10.0.0.0/8 out";
            system(exec_this.toStdString().c_str());
            //qDebug()<<exec_this<<endl;
        }
        QSqlQuery query("INSERT INTO `freenibs`.`updown` (`ip` ,`time` ,`status`) VALUES ( '"+ip+"',CURRENT_TIMESTAMP , 'up');");

     }
     db.close();
//    std::cout<<"HELLO";
//    return a.exec();
}

