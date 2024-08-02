<?php
//all function needed to get data from or communicate with mysql database via sql request

class DataLayer
{

    private $connexion;

    function __construct() //connexion to db with API PDO
    {
        try {
            $this->connexion = new PDO("mysql:host=" . HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
            //echo "connexion à la base de données réussie";
        } catch (PDOException $th) {
            echo $th->getMessage();
        }
    }

    /**
     * fonction qui créer un projet en base de données
     * @param projectName le nom du projet
     * @param projectDescription la description du projet
     * @param members_emails Tableau contenant les emails des différents membres
     * @param projectDelay le delai d'éxécution du projet
     * @return j le nombre d'opération effectuée
     * @return NULL s'il y a une exception déclenchée 
     */
    function createNewProject($projectName, $projectDescription, $members_emails, $projectDelay)
    {
        $j = 0;
        for ($i = 0; $i < count($members_emails); $i++) {
            $sql_1 = "SELECT id FROM members WHERE email = :email";
            $sql = "INSERT INTO `project`(`name`, `description`, `member_id`, `delay`) VALUES (:name, :description, :member_id, :delay)";

            try {
                $result = $this->connexion->prepare($sql_1);
                $var_1 = $result->execute(array(':email' => $members_emails[$i]));
                $id = $result->fetchAll(PDO::FETCH_ASSOC);

                // print_r($id[0]["id"]);
                // exit();
                if ($id) {
                    $result = $this->connexion->prepare($sql);
                    $var = $result->execute(array(
                        ':name' => $projectName,
                        ':description' => $projectDescription,
                        ':member_id' => $id[0]["id"],
                        ':delay' => $projectDelay
                    ));
                    // print_r($var);
                    // exit();
                    if ($var) {
                        $j++;
                    }
                }
            } catch (PDOException $th) {
                return NULL;
            }
        }
        // print_r($j);
        // exit();
        return $j;
    }

    /**
     * fonction qui sert à récupérer les projets au sein de la base de données
     * @param rien ne prend pas de paramètre
     * @return array tableau contenant les prjets, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getProject()
    {
        $sql = "SELECT *, COUNT(id) AS nbre_membre FROM project GROUP BY name ORDER BY id DESC";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui sert à récupérer les projets au sein de la base de données
     * @param rien ne prend pas de paramètre
     * @return array tableau contenant les prjets, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getProjectFull()
    {
        $sql = "SELECT * FROM project ORDER BY create_at DESC";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui sert à récupérer les progression au sein de la base de données
     * @param rien ne prend pas de paramètre
     * @return array tableau contenant des progression, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getProgress()
    {
        $sql = "SELECT * FROM progress";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui sert à récupérer les membres au sein de la base de données
     * @param rien ne prend pas de paramètre
     * @return array tableau contenant les membres, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getMembers()
    {
        $sql = "SELECT * FROM members";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }


    /**
     * fonction qui permet d'authentifier un member
     * @param email l'email du customer
     * @param password le mot de passe du customer
     * @return ARRAY tableau contenant les infos du user si authentification réussie
     * @return FALSE si authentification échouée
     * @return NULL s'il y a une exception déclenchée 
     */
    function authentifier($email, $password)
    {
        $sql = "SELECT * FROM members WHERE email = :email";
        try {
            $result = $this->connexion->prepare($sql);
            $result->execute(array(':email' => $email));
            $data = $result->fetch(PDO::FETCH_ASSOC);
            if ($data && ($data['password'] == sha1($password))) {
                unset($data['password']);
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui créer un projet en base de données
     * @param projectName le nom du projet
     * @param projectDescription la description du projet
     * @param member_email Tableau contenant les emails des différents membres
     * @return TRUE si l'enregistrement s'est bien passé, FALSE sinon 
     * @return NULL s'il y a une exception déclenchée 
     */
    function addTask($projectName, $projectDescription, $member_email)
    {
        $sql_1 = "SELECT id FROM members WHERE email = :email";
        $sql = "INSERT INTO `tasks`(`task`, `project_name`, `performer_id`) VALUES (:task, :project_name, :performer_id)";

        try {
            $result = $this->connexion->prepare($sql_1);
            $var_1 = $result->execute(array(':email' => $member_email));
            $id = $result->fetchAll(PDO::FETCH_ASSOC);

            // print_r($id[0]["id"]);
            // exit();
            if ($id) {
                $result = $this->connexion->prepare($sql);
                $var = $result->execute(array(
                    ':task' => $projectDescription,
                    ':project_name' => $projectName,
                    ':performer_id' => $id[0]["id"]
                ));
                // print_r($var);
                // exit();
                if ($var) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui sert à récupérer les tasks au sein de la base de données
     * @param member_email prend l'email en paramètre
     * @return array tableau contenant les prjets, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getTask($member_email)
    {
        // $sql_1 = "SELECT id FROM members WHERE email = :email";
        $sql_1 = "SELECT * FROM tasks INNER JOIN members ON tasks.performer_id = members.id WHERE members.email = :email";
        // print_r($sql_1);
        // exit();
        try {
            $result = $this->connexion->prepare($sql_1);
            $var = $result->execute(array(':email' => $member_email));
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            if ($data) {
                unset($data[0]["password"]);
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui sert à realiser des tasks
     * @param member_email prend l'email en paramètre
     * @return TRUE si opération réalisée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function doTask($taskId)
    {
        $sql_1 = "SELECT progress_id FROM tasks WHERE id_task = :id_task";
        $sql = "UPDATE tasks SET progress_id = :progress_id WHERE id_task = :id_task";
        // print_r($sql_1);
        // exit();
        try {
            $result = $this->connexion->prepare($sql_1);
            $var = $result->execute(array(':id_task' => $taskId));
            $progress_id = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($progress_id) {
                $result = $this->connexion->prepare($sql);
                $var = $result->execute(array(
                    ':progress_id' => $progress_id[0]["progress_id"] + 1,
                    ':id_task' => $taskId
                ));
                if ($var) {
                    return $var;
                } else {
                    return FALSE;
                }
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui sert à calculer la progression des projets
     * @param rien ne prend pas de paramètre
     * @return array si opération réalisée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function Projectprogress()
    {
        $sql_1 = "SELECT *, SUM(progress_id) AS progress_tot, COUNT(project_name) AS nbre_task FROM tasks GROUP BY project_name";
        try {
            $result = $this->connexion->prepare($sql_1);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui sert à calculer la progression des projets
     * @param rien ne prend pas de paramètre
     * @return array si opération réalisée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function ProjectprogressBrut()
    {
        $sql_1 = "SELECT * FROM tasks";
        try {
            $result = $this->connexion->prepare($sql_1);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui sert à recuperer les entre deux membres
     * @param sender_Id l'id de l'expéditeur
     * @param receiver_Id l'id du destinataire
     * @return array si opération réalisée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getMessage($senderId, $receiverid)
    {
        $sql_1 = "SELECT *, DATE_FORMAT(create_at, '%d-%m-%Y %Hh%im%ss') AS date_pub FROM message WHERE (sender_id = :sender_id AND receiver_id = :receiver_id) OR (sender_id = :receiver_id AND receiver_id = :sender_id) ORDER BY create_at";
        try {
            $result = $this->connexion->prepare($sql_1);
            $var = $result->execute(array(
                ":sender_id" => $senderId,
                ":receiver_id" => $receiverid
            ));
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui sert envoyer un message
     * @param sender_Id l'id de l'expéditeur
     * @param receiver_Id l'id du destinataire
     * @return TRUE si l'opération s'est déroulée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function sendMessage($senderId, $receiverid, $messageBody)
    {
        $sql_1 = "INSERT INTO `message`(`message`, `sender_id`, `receiver_id`) VALUES (:message, :sender_id, :receiver_id)";
        try {
            $result = $this->connexion->prepare($sql_1);
            $var = $result->execute(array(
                ":sender_id" => $senderId,
                ":receiver_id" => $receiverid,
                ":message" => $messageBody
            ));

            if ($var) {
                return $var;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui créer un member en base de données
     * @param email l'email du user
     * @param password le mot de passe du user
     * @return TRUE sien cas de création avec succès du customer, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function createUser($email, $role, $password)
    {
        $sql = "INSERT INTO members (email,role, password) VALUES (:email, :role, :password)";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute(array(
                ':email' => $email,
                ':role' => $role,
                ':password' => sha1($password)
            ));
            if ($var) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
}
