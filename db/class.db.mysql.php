<?php

//////////////////////////////////////////////////////////////////////////////
//
//  Klasse f�r den Zugriff auf eine MySQL 4.1 Datenbank. Diese Klasse benutzt
//  die neue MySQL-I Erweiterung f�r den speziellen Zugriff auf MySQL 4.1
//  Funktionen.
//
//  Erstellt        : FD, 2003-09-02
//  Letzte �nderung : FD, 2004-11-20: Umstellung auf PHP 5 OOP
//
//////////////////////////////////////////////////////////////////////////////

class db {
        
  private $server       = "";
  private $user         = "";
  private $password     = "";
  private $database     = "";
  
  private $link         = null;
  private $query        = "";
  private $result       = null;
  
  private $queryCount   = 0;
  private $clock_start  = 0;
  private $clock_stopp  = 0;
  
  private $displayError = 0;
  private $logError     = 1;
  private $countTime    = 1;

  private $error        = false;
  private $error_no     = 0;
  private $error_msg    = "";  
  
  /* FD, 2004-11-20: Konstruktor-Methode: Hier werden die Verbindungsdaten �bergeben und anschlie�end automatisch mit der Datenbank verbunden.
                     Parameter: 1. String, Adresse des Servers
                                2. String, Benutzername
                                3. String, Passwort
                                4. String, Name der Datenbank, zu der verbunden werden soll
                                5. Boolean, ob Fehler angezeigt werden sollen
                                6. Boolean, ob Fehler geloggt werden sollen
                                7. Boolean, ob die Zeit bis zur einer Antwort eines Queries gemessen werden soll.
                     R�ckgabe : Keine
  */
  function __construct($server, $user, $password, $database, $displayError, $logError, $countTime) {
    $this->server       = $server;
    $this->user         = $user;
    $this->password     = $password;
    $this->database     = $database;
    
    // FD, 2004-08-10: Die globalen Variablen setzen. Wenn bei einem Query keinen anderen Werte angegeben sind, dann z�hlen diese hier.
    $this->displayError = $displayError;
    $this->logError     = $logError;
    $this->countTime    = $countTime;
    
    // FD, 2004-08-07: Jetzt automatisch zur Datenbank verbinden.
    $this->dbConnect();
  }    
  
  /* FD, 2004-11-20: Dekonstruktor-Methode: Diese Methode wird immer aufgerufen, wenn das Objekt vernichtet wird.
                     Es beendet die noch bestehende Datenbankverbindung.
                     Parameter: Keine
                     R�ckgabe : Keine
  */ 
  /*function __destruct() {
  	if ($this->link != null)
    	$this->link->close();  
  }*/
  
  /* FD, 2004-08-07: Methode, um zu der angegebenen Datenbank zu verbinden.
                     Parameter: Keine
                     R�ckgabe : Keine
  */
  private final function dbConnect() {
    $this->link = new mysqli($this->server, $this->user, $this->password, $this->database);
  	
    $this->newQuery("SET NAMES 'UTF8'");
    
    if (mysqli_connect_errno()) {
      $this->setError();
    }
  }
 
  /* FD, 2004-08-09: Methode, um ein neues Query an die Datenbank zu senden. Hier besteht die M�glichkeit, dass Query zu z�hlen, die Zeit
                     bis zur Antwort zu messen und die Entscheidung zu treffen, ob ein Fehler ausgegeben oder geloggt wird.
                     Parameter: 1. String, die eigentliche SQL-Anweisung
                                2. Boolean, ob der Fehler angezeigt werden soll oder nicht
                                3. Boolean, ob der Fehler in die Error-Log geschrieben werden soll oder nicht
                                4. Boolean, ob die Zeit bis zur Antwort gemessen werden soll
                     R�ckgabe : Keine
  */
  public final function newQuery($query, $displayError = false, $logError = false, $countTime = false) {
    // FD, 2004-08-10: Falls die �bergebenen Variablen leer sind, dann werden die globalen benutzt.
    if (!$displayError) {
      $displayError = $this->displayError;
    }
    
    if (!$logError) {
      $logError = $this->logError;
    }
    
    if (!$countTime) {
      $countTime = $this->countTime;
    }
    
    $this->query = trim($query);
    
    if ($countTime) {
      $this->setClockStart();
      $this->result = $this->link->query($this->query);
      $this->setClockStopp();
    } else {
      $this->result = $this->link->query($this->query);
    }
    
    $this->queryCount++;
    
    if(!$this->result) {
      $this->setError();
    }        
  }

  public function escapeInput($input) {
    return $this->link->real_escape_string($input);
  }
   
  /* FD, 2004-11-20: Diese Methode dient zum holen eines Resultssets. Es werden die Modi MYSQI_ASSOC, MYSQLI_NUM und MYSQLI_BOTH
                     unterst�tzt. Standard ist MYSQLI_ASSOC, was ein assoziatives array zur�ckgibt.
                     Parameter: 1. Konstante, Modus: Assoziatives Array (*_ASSOC)
                                                     Numerisches Array (*_NUM)
                                                     Beides (*_BOTH)
                     R�ckgabe : 1. Array mit dem Resultset
  */
  public final function getResults($mode = MYSQLI_ASSOC) {
    return ($this->getError()) ? null : $this->result->fetch_array($mode);
  } 
  
  public final function getObjectResults() {
  	return ($this->getError()) ? null : $this->result->fetch_object();
  }
  
  /* FD, 2004-11-20: Diese Methode gibt nur einen Wert einer Ergebnismenge zur�ck. Das ist hilfreich, wenn man nur einen
                     Wert in einer SQL-Abfrage holt.
                     Parameter: Keine
                     R�ckgabe : 1. Variant, der in der Ergebnismenge befindliche Wert
  */
  public final function getResult() {
    if (!$this->getError() && $this->getRowCount() > 0) {
      $result = $this->result->fetch_row();
      return $result[0];
    } else {
      return null;
    }   
  }
  
  /* FD, 2004-11-20: Diese Methode gibt die Anzahl der Zeilen einer Ergebnismenge zur�ck.
                     Parameter: Keine
                     R�ckgabe : 1. Integer, Anzal der Zeilen
  */
  public final function getRowCount() {
    return ($this->getError()) ? -1 : $this->result->num_rows;
  }

  /* FD, 2004-11-20: Diese Methode gibt die betroffenden Zeilen zum Beispiel einer Update-Anweisung zur�ck.
                     Parameter: Keine
                     R�ckgabe : Integer, Anzahl der betroffenden Zeilen
  */
  public final function getAffectedRowCount() {
    return ($this->getError()) ? -1 : $this->link->affected_rows;
  }
   
  /* FD, 2004-11-20: Diese Methode gibt die zuletzt eingef�gt ID eines Auto_Increment Feldes zur�ck.
                     Parameter: Keine
                     R�ckgabe : Integer, die letzte ID eines Auto_Increment Feldes
  */
  public final function getLastInsertID() {
    return ($this->getError()) ? -1 : $this->link->insert_id;
  }
  
  /* FD, 2004-11-20: Diese Methode gibt die Anzahl der bis jetzt durchgef�hrten SQL-Queries aus.
                     Parameter: Keine
                     R�ckgabe : Integer, Anzahl der durchgef�hrten Queries
  */
  public final function getQueryCount() {
    return $this->queryCount;
  }
        
  /* FD, 2004-11-20: Diese Methode l�scht eine Ergebnismenge und gibt den benutzen Speicher wieder frei.
                     Parameter: Keine
                     R�ckgabe : Keine
  */
  public final function freeResult() {
    if (!$this->getError()) {
      $this->result->free();
    }
  }
  
  /* FD, 2004-11-20: Diese Methode startet die Zeitmessung. Sie speichert die Startzeit in der Klassenvariablen clock_start.
                     Parameter: Keine
                     R�ckgabe : Keine
  */
  private final function setClockStart() {
    list($usec,$sec)=explode(' ',microtime());
    $this->clock_start += $usec+$sec;
  }

  /* FD, 2004-11-20: Diese Methode stoppt die Zeitmessung. Sie speichert die Stoppzeit in der Klassenvariablen clock_stop.
                     Parameter: Keine
                     R�ckgabe : Keine
  */
  private final function setClockStopp() {
    list($usec,$sec)=explode(' ', microtime());
    $this->clock_stopp += $usec+$sec;
  } 
  
  /* FD, 2004-11-20: Diese Methode berechnet die Zeitdifferenz zwischen der Start- und der Stoppzeit. Dadurch l�sst sich ermitteln,
                     wie lange die Ausf�hrungszeit eines Queries war.
                     Parameter: Integer, Anzahl der Nachkommastellen
                     R�ckgabe : Float, die berechnete Zeitdifferenz
  */
  public final function getQueryExeTime($precision = 4) {
    return round($this->clock_stopp - $this->clock_start, $precision);
  }
  
  public function getQuery() {
  	return $this->query;
  }
  
  
  private final function setError() {
    $this->error     = true;
    
    if (isset($this->link) && $this->link != null) {
    	$this->error_no  = $this->link->errno;
    	$this->error_msg = $this->link->error;
  	}
  }
  
  public final function getError() {
    return $this->error;
  }
        
  public final function getErrorMsg() {
    if($this->getError()) {
        $error_str  = "<pre>";
        $error_str .= "<b>SQL-Database ERROR</b>\n\n";
        $error_str .= "Query:      <b>".$this->query."</b>\n";
        $error_str .= "Message:    <b>".$this->error_msg."</b>\n";
        $error_str .= "Error-Code: <b>".$this->error_no."</b>\n";
        $error_str .= "Date:       <b>".date("d.m.Y - H:i")."</b>\n";
        $error_str .= "Script:     <b>".$_SERVER['PHP_SELF']."</b>\n";
        $error_str .= "</pre>";
    } else {
      $error_str = "Kein Fehler aufgetreten.";
    }
    return $error_str;
  } 
  
}

?>
