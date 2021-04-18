<?php
/**
 * Creates a JSON page based on the parameters
 *
 * @author YOUR NAME
 *
 */
class JSONpage {
    private $page;
    private $recordset;

    /**
     * @param $pathArr - an array containing the route information
     * @param $recordset
     */
    public function __construct($pathArr, $recordset) {
        $this->recordset = $recordset;
        $path = (empty($pathArr[1])) ? "api" : $pathArr[1];

        switch ($path) {
            case 'api':
                $this->page = $this->json_welcome();
                break;
            case 'userRanking':
                $this->page = $this->json_user_ranking();
                break;
            case 'games':
                $this->page = $this->json_games();
                break;
            case 'questions':
                $this->page = $this->json_questions();
                break;
            default:
                $this->page = $this->json_error();
                break;
        }
    }

//an arbitrary max length of 20 is set
    private function sanitiseString($x) {
        return substr(trim(filter_var($x, FILTER_SANITIZE_STRING)), 0, 20);
    }

//an arbitrary max range of 1000 is set
    private function sanitiseNum($x) {
        return filter_var($x, FILTER_VALIDATE_INT, array("options"=>array("min_range"=>0, "max_range"=>1000)));
    }

    private function json_welcome() {
        $msg = array("message"=>"welcome", "author"=>"Damilola Thomas");
        return json_encode($msg);
    }

    private function json_error() {
        $msg = array("message"=>"Hello user, you have visited the wrong url, please try another");
        $devmsg = array("message"=>"Hello user, you have visited the wrong url, please try another \n");
        logError($devmsg);
        return json_encode($msg);
    }

    /**
     * json_actors
     *
     * @todo this function can be improved
     */
    private function json_user_ranking() {
        $query  = "Select username, firstname, surname, sum(questionXPEarned) AS points
                    from Users
                    LEFT join QuestionsCompleted on Users.UserId = QuestionsCompleted.userID
                    GROUP BY username
                    ORDER BY QuestionXPEarned DESC";
        $params = [];

        if (isset($_REQUEST['search'])) {
            $query .= " WHERE username LIKE :term";
            $term = $this->sanitiseString("%".$_REQUEST['search']."%");
            $params = ["term" => $term];
        }

        if (isset($_REQUEST['page'])) {
            $query .= " ORDER BY username";
            $query .= " LIMIT 10 ";
            $query .= " OFFSET ";
            $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
        }

        return ($this->recordset->getJSONRecordSet($query, $params));
    }

    /**
     * json_films
     *
     * @todo this function can be improved
     */
    private function json_games() {
        $query  = "SELECT GameName, gameDescription FROM Games";
        $params = [];

        if (isset($_REQUEST['search'])) {
            $query .= " WHERE GameName LIKE :term";
            $term = $this->sanitiseString("%".$_REQUEST['search']."%");
            $params = ["term" => $term];
        }

        if (isset($_REQUEST['page'])) {
            $query .= " ORDER BY title";
            $query .= " LIMIT 10 ";
            $query .= " OFFSET ";
            $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
        }

        return ($this->recordset->getJSONRecordSet($query, $params));
    }

    private function json_questions () {
        $query = "SELECT questionTitle, questionAnswer, questionType, questionMaxXP FROM  Questions";
        $params = [];

        if (isset($_REQUEST['search'])) {
            $query .= "WHERE questionType LIKE :term";
            $term = $this->sanitiseString("%".$_REQUEST['search']."%");
            $params = ["term" => $term];
        }
        if (isset($_REQUEST['page'])) {
            $query .= " ORDER BY questionTitle";
            $query .= " LIMIT 10 ";
            $query .= " OFFSET ";
            $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
        }
        return ($this->recordset->getJSONRecordSet($query, $params));

    }



    public function get_page() {
        return $this->page;
    }
}
?>