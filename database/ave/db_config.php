<?php

    class Dbconfig {

        protected $user;
        protected $password;
        protected $dbname;

        function Dbconfig() {

			$ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_ave/database/ave/app.ini');

			if ($ini['env'] == 'production') {

				$this->user = 'vulnerabilidad';
	            $this->password = 'v7gekspn';
	            $this->dbName = '   (DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.23.50.95)(PORT = 1521)))(CONNECT_DATA = (SERVICE_NAME = CATGIS)))';

			}else{

				$this->user = 'vulnerabilidad';
	            $this->password = 'v7gekspn';
	            $this->dbName = '   (DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.23.25.27)(PORT = 1521)))(CONNECT_DATA = (SERVICE_NAME = PRUEBAS)))';

			}

        }
    }

?>
