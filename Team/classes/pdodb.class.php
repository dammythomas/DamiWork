<?php
/**
 * PDO (PHP Data Objects) Database Connection
 * Create a singleton SQLite database connection
 * @author John Rooksby (using the module tutor's name will mean he gets marks for your work instead of you)
 */
class PDOdb {
    private static $dbConnection = null;

    /**
     * Make this private to prevent normal class instantiation
     * @access private
     */
    private function __construct() {
    }

    /**
     * Return DB connection or create initial connection
     * @return object (PDO)
     * @access public
     * @todo the catch block should not directly 'echo' output. This will 'cost' you in the coursework.
     */
    public static function getConnection($dbname) {
        $servername = "65.19.143.6";
        $username = "uniproj2_dami";
        $password = "Damilola1";
        $db= $dbname;
        $dsn = "mysql:host=$servername;dbname=$db;";
        if ( !self::$dbConnection ) {
            try {
                self::$dbConnection = new PDO($dsn,$username,$password);
                self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch( PDOException $e ) {
                echo $e->getMessage();
            }
        }
        return self::$dbConnection;
    }
}
?>