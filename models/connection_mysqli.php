<?php

	//Esta es la clase de coneccion Padre que hereda los atributos a los modelos
	abstract class Connection
	{
		protected $connection;

		//Abre conexion a base de datos al momento de instanciarse la clase
		public function __construct()
		{
			//Cuidado con estas líneas de terror			
			if(!$this->connection = mysqli_connect(SERVIDOR,USUARIO_BD,CONTRASENIA_BD,BD))
			{
				echo "<br><b style='color:red;'>Error al tratar de conectar</b><br>";	
			}
			$this->connection->set_charset('utf8');// Previniendo errores con SetCharset
			//echo 'Conectado';
		}

		//Cierra la conexion al momento de dejar de usar la clase de conexion
		public function __destruct()
		{
			$this->connection->close();
			//echo 'Desconectado';
		}

		//Funcion que genera las consultas genericas a la base de datos
		public function query($query)
		{
		
			$result = $this->connection->query($query) or die("<b style='color:red;'>Error en la consulta.</b><br /><br />".$this->connection->error."<be>Error:<br>".$query);
			return $result;
		}
		
		public function multi_query($query)
		{
		
			$result = $this->connection->multi_query($query) or die("<b style='color:red;'>Error en la consulta.</b><br /><br />".$this->connection->error."<be>Error:<br>".$query);
			return $result;
		}

		public function insert_id($query)
		{
			if(stristr($query, 'insert'))
			{
				$this->connection->query($query) or die("<b style='color:red;'>Error en la consulta.</b><br /><br />".$this->connection->error."<be>Error:<br>".$query);
				return $this->connection->insert_id;
			}
			else
			{
				return "La consulta no incluye un INSERT.";
			}
		}
		//Metodo para generar transaccion con la base de datos
		public function dataTransact($data)
		{
			$this->connection->autocommit(false);
			if($this->connection->query('BEGIN;'))
			{
				if($this->connection->multi_query($data))
				{
					do {
						/* almacenar primer juego de resultados */
						if ($result = $this->connection->store_result()) {
							while ($row = $result->fetch_row()) {
								echo $row[0];
							}
							$result->free();
						}

					} while ($this->connection->more_results() && $this->connection->next_result());

					$this->connection->commit();
					return true;
				}
				else
				{
					$error = $this->connection->error;
					//echo "Chiales esto trono!";
					$this->connection->rollback();
					return $error;
				}		
			}
			else
			{
				$error = $this->connection->error;
				$this->connection->rollback();
				return $error;
			}
		}
		//Termina transaccion-----------
	}

