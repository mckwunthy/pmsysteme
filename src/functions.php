<?php
function verifParams()
{
  if (isset($_POST) && sizeof($_POST) > 0) {
    foreach ($_POST as $key => $value) {
      $data = trim($value);
      $data = stripslashes($data);
      $data = strip_tags($data);
      $data = htmlspecialchars($data);
      $_POST[$key] = $data;
    }
  }
}

function checkData($tab)
{
  $result_books = array();
  $data = array();

  foreach ($tab as $key => $value) {
    $value = trim($value);
    $data[$key] = $value;

    //GENERAL
    if ($value === "") {
      $result_books[$key] = "Le champs " . $key . "  ne peut pas être vide";
    }

    //SPECIFIQUE
    if ($key == "projectName" && !isset($result_books[$key])) {
      if (strlen($value) < 5) {
        $result_books[$key] = "Le nom doit faire au moins 5 caratères !";
      }
      if (strlen($value) > 30) {
        $result_books[$key] = "Le nom doit faire au maxium 30 caratères !";
      }
    }

    if ($key == "membersEmail" && !isset($result_books[$key])) {
      $model = '/^([a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,8}[,])+$/';

      if (!preg_match($model, $value)) {
        $result_books[$key] = "mettez une virgule (,) sans espace à la fin de chaque email !";
      }
    }

    if ($key == "projectDelay" && !isset($result_books[$key])) {
      if (!(preg_match('/[0-9]{1,}/', $value))) {
        $result_books[$key] = "veullez saisir une chiffre correcte !";
      }
      if ($value < 0) {
        $result_books[$key] = "le delai ne peut etre négatif !";
      }
    }
    if ($key == "projectDescription" && !isset($result_books[$key])) {
      if (strlen($value) > 100) {
        $result_books[$key] = "le resume doit compter au plus de 100 caractères !";
      }
      if (strlen($value) < 20) {
        $result_books[$key] = "le resume doit compter au moins 20 caractères !";
      }
    }
  }

  return [$result_books, $data];
}

function displayConnexion()
{
  $result = '
  <div class="row home">
    <div class="col-5">
      <form method="POST" action="authentification" class="formAuthent">
        <div class="input-group mb-3">
          <span class="input-group-text" id="inputGroup-sizing-default">Email</span>
          <input type="email" name="email" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text" id="inputGroup-sizing-default">Password</span>
          <input type="password" name="password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
        </div>
        <div class="d-grid gap-2">
          <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <button type="submit" class="btn btn-warning fw-bold">Sing in</button>
            <button type="button" class="btn btn-success fw-bold createAccount">Create account</button>
          </div>
        </div>
      </form>
    </div>
    <div class="col-7">
      <div class="text-center fs-1 fw-bold text-dark">
        Bienvenue sur notre systeme de gestion de projet intégré
      </div>
      <div>
        <img src="images' . SP . 'bg-2.jpg" alt="illustration" class="bg-1"/>
        <div class="mckwunthy">@by_mckwunthy</div>
      </div>
    </div>
  </div>
  ';
  return $result;
}

function displayAuthentification()
{
  //on charges les donnees du user
  global $model;
  //redirection : protection
  if (!isset($_POST["email"]) || !isset($_POST["password"])) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  $authentData = $model->authentifier($_POST["email"], $_POST["password"]);

  $result = '';
  if (!$authentData) {
    $result .= '
    <div class="d-grid gap-2">
      <button class="btn btn-danger" type="button">Echec de connexion, paramètres incorrects !</button>
    </div>
    ';
    $result .= displayConnexion();
    return $result;
  } else {
    $_SESSION["user"] = [];
    foreach ($authentData as $key => $value) {
      $_SESSION["user"][$key] = $value;
    }
    //on sauvegarde les infos en json dans un fichier cree
    //le fichier sera efface lors de la deconnexion
    $filename_m = SRC . SP . "json" . SP . $_SESSION["user"]["email"] . "m.json";
    $member_json = json_encode($_SESSION["user"]);
    file_put_contents($filename_m, $member_json);

    $result .= displayCreateproject();
    return $result;
  }
}

function displayDeconnexion()
{
  //on supprime les fichiers json lors de la deconnexion
  $filename_m = SRC . SP . "json" . SP . $_SESSION["user"]["email"] . "m.json";
  $filename_p = SRC . SP . "json" . SP . $_SESSION["user"]["email"] . "p.json";
  $filename_mb = SRC . SP . "json" . SP . $_SESSION["user"]["email"] . "mb.json";
  if (file_exists($filename_m)) {
    unlink($filename_m);
  }
  if (file_exists($filename_p)) {
    unlink($filename_p);
  }
  if (file_exists($filename_mb)) {
    unlink($filename_mb);
  }
  session_destroy();
  header('Location: ' . BASE_URL . SP . 'connexion');
}


function displayCreateproject()
{
  //redirection : protection
  if (!isset($_SESSION["user"])) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }
  global $model;
  global $project;

  if (isset($_POST["createProjectBt"])) {
    $error_and_data = checkData($_POST);
  }

  //creation du projet
  if (isset($_POST["createProjectBt"]) && empty($error_and_data[0])) {

    //enregistrer les emails des membres dans une table
    $members_emails = [];
    $members_emails = trim($error_and_data[1]["membersEmail"], ',');
    $members_emails = explode(',', $error_and_data[1]["membersEmail"]);
    unset($members_emails[count($members_emails) - 1]);

    //enregistrement des donnees en bdd
    $createProjectResult = $model->createNewProject($error_and_data[1]["projectName"], $error_and_data[1]["projectDescription"], $members_emails, $error_and_data[1]["projectDelay"]);
    // print_r($createProjectResult);
    // exit();
    if ($createProjectResult) {
      echo '
        <div class="d-grid gap-2">
          <button class="btn btn-success" type="button">Succes - Project create with ' . $createProjectResult . ' members</button>
        </div>
        ';
    } else {
      echo '
        <div class="d-grid gap-2">
          <button class="btn btn-danger" type="button">Failure to create project !</button>
        </div>
        ';
    }
  } else {
    $createProjectResult = NULL;
  }



  //error
  $error_projectName = isset($error_and_data[0]["projectName"]) && !empty($error_and_data[0]["projectName"]) ? $error_and_data[0]["projectName"] : null;
  $error_projectDescription = isset($error_and_data[0]["projectDescription"]) && !empty($error_and_data[0]["projectDescription"]) ? $error_and_data[0]["projectDescription"] : null;
  $error_membersEmail = isset($error_and_data[0]["membersEmail"]) && !empty($error_and_data[0]["membersEmail"]) ? $error_and_data[0]["membersEmail"] : null;
  $error_projectDelay = isset($error_and_data[0]["projectDelay"]) && !empty($error_and_data[0]["projectDelay"]) ? $error_and_data[0]["projectDelay"] : null;


  //value
  $value_projectName = isset($error_and_data[1]["projectName"]) && !empty($error_and_data[1]["projectName"]) && empty($error_and_data[2]) ? $error_and_data[1]["projectName"] : null;
  $value_projectDescription = isset($error_and_data[1]["projectDescription"]) && !empty($error_and_data[1]["projectDescription"]) && empty($error_and_data[2]) ? $error_and_data[1]["projectDescription"] : null;
  $value_membersEmail = isset($error_and_data[1]["membersEmail"]) && !empty($error_and_data[1]["membersEmail"]) && empty($error_and_data[2]) ? $error_and_data[1]["membersEmail"] : null;
  $value_projectDelay = isset($error_and_data[1]["projectDelay"]) && !empty($error_and_data[1]["projectDelay"]) && empty($error_and_data[2]) ? $error_and_data[1]["projectDelay"] : null;

  $result = '
  <div class="row createproject">
    <div class="col-5 box-left">
      <div class="titleBand">create new project</div>
      <form method="POST" action="createproject" class="formCreatProject">
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Project name</span>
            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="' . $value_projectName . '" name="projectName" required>
          </div>
          <div class="error">' . $error_projectName . '</div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Description</span>
            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="' . $value_projectDescription . '" name="projectDescription" required>
          </div>
          <div class="error">' . $error_projectDescription . '</div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Members emails</span>
            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="' . $value_membersEmail . '" name="membersEmail" required>
            </div>
            <div class="infos">Mettez une <strong>virgules (,)</strong> sans espaces à la fin de chaque email (<strong>ex.: email_1,email_2,</strong>)</div>
          <div class="error">' . $error_membersEmail . '</div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Delay (months)</span>
            <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="' . $value_projectDelay . '" name="projectDelay" required>
          </div>
          <div class="error">' . $error_projectDelay . '</div>
          <div class="d-grid gap-2">
            <input type="submit" class="btn btn-warning fw-bold" value="Create" name="createProjectBt"/>
          </div>
        </form>
    </div>
    <div class="col-7">
       <div class="titleBand">projects</div>
       <div class="table">
        <table>
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Delay (months)</th>
              <th scope="col">Created at</th>
              <th scope="col">Members</th>
            </tr>
          </thead>
          <tbody>';
  if ($project) {
    foreach ($project as $key => $value) {
      $result .= '
      <tr>
         <td scope="col">' . $key + 1 . '</td>
         <td scope="col">' . $value["name"] . '</td>
         <td scope="col">' . $value["description"] . '</td>
         <td scope="col">' . $value["delay"] . '</td>
         <td scope="col">' . $value["create_at"] . '</td>
         <td scope="col">' . $value["nbre_membre"] . '</td>
       </tr>';
    }
  }

  $result .= '
          </tbody>
           <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Delay (months)</th>
              <th scope="col">Created at</th>
              <th scope="col">Membrers</th>
            </tr>
          <tfoot>
          </tfoot>
        </table>
       </div>
    </div>
  </div>
  ';
  return $result;
}

function displayManagetask()
{
  //redirection : protection
  if (!isset($_SESSION["user"])) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  global $model;
  global $project;
  global $projectFull;
  global $members;
  global $progression;

  //on sauvegarde les projects en json dans un fichier cree
  //le fichier sera efface lors de la deconnexion
  $filename_p = SRC . SP . "json" . SP . $_SESSION["user"]["email"] . "p.json";
  $member_json = json_encode($projectFull);
  file_put_contents($filename_p, $member_json);

  $filename_mb = SRC . SP . "json" . SP . $_SESSION["user"]["email"] . "mb.json";
  $member_json = json_encode($members);
  file_put_contents($filename_mb, $member_json);

  //creation du task
  if (isset($_POST["addTask"]) && !empty($_POST["task"])) {
    //enregistrement des donnees en bdd
    $createTaskResult = $model->addTask($_POST["name"], $_POST["task"], $_POST["email"]);
    // print_r($createTaskResult);
    // exit();
    if ($createTaskResult) {
      echo '
        <div class="d-grid gap-2">
          <button class="btn btn-success" type="button">Succes - Task created</button>
        </div>
        ';
    } else {
      echo '
        <div class="d-grid gap-2">
          <button class="btn btn-danger" type="button">Failure to create Task !</button>
        </div>
        ';
    }
  } else {
    $createTaskResult = NULL;
  }

  //add task
  if (isset($_POST["doTaskSmt"])) {
    $addTaskResult = $model->doTask($_POST["taskId"]);
    // print_r($addTaskResult);
    // exit();
    if ($addTaskResult) {
      echo '
        <div class="d-grid gap-2">
          <button class="btn btn-success" type="button">Succes - Task Done</button>
        </div>
        ';
    } else {
      echo '
        <div class="d-grid gap-2">
          <button class="btn btn-danger" type="button">Fail to do task !</button>
        </div>
        ';
    }
  }

  $result = '
  <div class="row manageTask">
    <div class="col-5 box-left">
      <div class="titleBand">Add Task to project</div>
      <form method="POST" action="managetask" class="formAddTask">
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Project name</span>
                <select class="form-select selectProject" name="name" aria-label="Default select example" required>
                    <option selected>choose project</option>';
  if (!empty($project)) {
    foreach ($project as $key => $value) {
      $result .= '<option value="' . $value["name"] . '">' . strtoupper($value["name"]) . '</option>';
    }
  }
  $result .= '
                </select>
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Member</span>
                <select class="form-select selectMember" name="email" aria-label="Default select example">
                    <option selected>choose member</option>
                </select>
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Task</span>
            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="task" required>
          </div>
          <div class="d-grid gap-2">
            <input type="submit" class="btn btn-warning fw-bold" value="AddTask" name="addTask"/>
          </div>
        </form>
    </div>
    <div class="col-7">
       <div class="titleBand">Do Task  & Tasks Progress</div>
       <div class="table">
        <table>
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Project</th>
              <th scope="col">Task</th>
              <th scope="col">Progress</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>';
  $tasksData = $model->getTask($_SESSION["user"]["email"]);
  // print_r($tasksData);
  // exit();
  if ($tasksData) {
    foreach ($tasksData as $key => $value) {
      $result .= '
      <tr>
         <td scope="col">' . $key + 1 . '</td>
         <td scope="col">' . $value["project_name"] . '</td>
         <td scope="col">' . $value["task"] . '</td>
         <td scope="col">' . $progression[$value["progress_id"] - 1]["progression"] . ' %</td>
         <td scope="col">
         ';
      if ($progression[$value["progress_id"] - 1]["progression"] != 100) {
        $result .= '
         <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <form method="POST" action="managetask" id="formDoTask">
                <input type="hidden" name="taskId" value="' . $value["id_task"] . '"/>
                <input type="submit" class="bg-warning border-0 me-1" name="doTaskSmt" value="Do Task"/>
              </form>
          </div>
         </td>
       </tr>';
      }
    }
  }

  $result .= '
          </tbody>
           <tfoot>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Project</th>
                <th scope="col">Task</th>
                <th scope="col">Progress</th>
                <th scope="col"></th>
              </tr>
          </tfoot>
        </table>
       </div>
    </div>
  </div>
  ';
  return $result;
}

function displayDashboard()
{
  //redirection : protection
  if (!isset($_SESSION["user"])) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  global $model;
  global $project;
  global $progression;
  global $members;
  $result = '
  <div class="row">
    <div class="col-3 leftBoxDashboard">
      <div>
        <img src="images' . SP . 'bg-3.png" alt="illustration" class="bg-1"/>
      </div>
    </div>
    <div class="col-9 dashboard">
       <div class="titleBand">DASHBOARD</div>
       <div class="table">
        <table>
        <thead>
            <tr>
              <th scope="col">id</th>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">members</th>
              <th scope="col">dealy</th>
              <th scope="col">created at</th>
              <th scope="col">Tasks</th>
              <th scope="col">progress</th>
            </tr>
          </thead>
          <tbody>';
  //donnee des tasks
  $taskData = $model->Projectprogress();
  if ($taskData) {

    // print_r($taskData);
    // exit();
    foreach ($project as $key => $value) {
      $result .= '
              <tr>
                <td scope="col">' . $value["id"] . '</td>
                <td scope="col">' . $value["name"] . '</td>
                <td scope="col">' . $value["description"] . '</td>
                <td scope="col">' . $value["nbre_membre"] . '</td>
                <td scope="col">' . $value["delay"] . '</td>
                <td scope="col">' . $value["create_at"] . '</td>
                <td scope="col">
                ';
      foreach ($taskData as $keyTask => $valueTassk) {
        if ($value["name"] == $valueTassk["project_name"]) {
          //codification id
          $str = $value["name"];
          $str = preg_replace('/ /', '', $str);
          // echo $str;

          $result .= $valueTassk["nbre_task"] . ' <i class="fa-solid fa-circle-plus " id="' . $str . '"></i>';
        }
      }
      $result .= '</td>
                <td scope="col">';
      foreach ($taskData as $keyTask => $valueTassk) {
        if ($value["name"] == $valueTassk["project_name"]) {
          $result .= ($progression[$valueTassk["progress_tot"] - 1]["progression"]) / $valueTassk["nbre_task"] . ' %';
        }
      }
      $result .= '</td>';
    }
  }

  $result .= '   
          </tbody>
          <tfoot>
              <tr>
                <th scope="col">id</th>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col">members</th>
                <th scope="col">dealy</th>
                <th scope="col">created at</th>
                <th scope="col">Tasks</th>
                <th scope="col">progress</th>
              </tr>
          </tfoot>
        </table>
       </div>
    </div>
  </div>
  ';

  $taskAffiche = '<div class="taskBox">';
  $taskFull = $model->ProjectprogressBrut();
  // print_r($taskFull);
  // exit();
  if (isset($taskFull) && !empty($taskFull)) {

    foreach ($taskFull as $key => $value) {
      //codification id
      $str = $value["project_name"];
      $str = preg_replace('/ /', '', $str);

      $taskAffiche .= '
    <div class="d-none ' . $str . '">
    <div class="taskaffichetitle">' . $value["project_name"] . '</div>
      <div class="taskAfiicheBox">
        <table>
       <thead>
            <tr>
              <th>#</th>
              <th>task</th>
              <th>performer</th>
              <th>progress</th>
            </tr>
        </thead>
        <tbody>
            <tr>
              <td>' . $key + 1 . '</td>
              <td>' . $value["task"] . '</td>
              <td>' . $members[$value["performer_id"] - 1]["email"] . '</td>
              <td>' . $progression[$value["progress_id"] - 1]["progression"] . ' %</td>
            </tr>
          </tbody>
        </table>
      </div>
  </div>
  ';
    }
  }

  return $result . $taskAffiche . '</div>';
}

function displayMessages()
{
  //redirection : protection
  if (!isset($_SESSION["user"])) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }
  global $members;
  global $model;
  // global $;

  //lire une conversation
  if (isset($_POST["btEcrire"])) {
    //recuperation id membre à partir email
    foreach ($members as $key => $value) {
      if ($value["email"] == $_SESSION["user"]["email"]) {
        $id_sender = $value["id"];
      }
      if ($value["email"] == $_POST["member_email"]) {
        $id_receiver = $value["id"];
      }
    }

    $msge = $model->getMessage($id_sender, $id_receiver);
  }


  //écrire un message / converser avec
  if (isset($_POST["sendMsgBt"]) && !empty($_POST["id_sender"]) && !empty($_POST["id_receiver"])) {
    // print_r($_POST);
    // exit();

    $msgSendResult = $model->sendMessage($_POST["id_sender"], $_POST["id_receiver"], $_POST["messageBody"]);
    if ($msgSendResult) {
      echo '
      <div class="d-grid gap-2">
        <button class="btn btn-success" type="button">Succès - message envoyé !</button>
      </div>
      ';
    } else {
      echo '
      <div class="d-grid gap-2">
        <button class="btn btn-danger" type="button">Echec d\'envoie !</button>
      </div>
      ';
    }
    //rechargement des messages
    $id_sender = $_POST["id_sender"];
    $id_receiver = $_POST["id_receiver"];
    $msge = $model->getMessage($id_sender, $id_receiver);
  }


  $result = '
  <div class="row message">
    <div class="col-4 emailBox">
      <div class="title">Membres emails</div>
      <div>';
  if ($members) {
    $result .= '<table class="messageTable">';
    foreach ($members as $key => $value) {
      $result .= '
        <tr>
          <td>' . $value["role"] . '</td>
          <td>' . $value["email"] . '</td>
          <td>
            <form action="messages" method="POST">
              <input type="hidden" name="member_email" value="' . $value["email"] . '"/>
              <input type="submit" value="msg" name="btEcrire"/>
            </form>
          </td>
        </tr>
      ';
    }
    $result .= '</table>';
  }
  $result .= '</div>
    </div>
    <div class="col-6 messageShowBox limit-b">
      <div class="title">conversations</div>
      <div class="messageContainer">';
  if (isset($msge) && !empty($msge)) {
    foreach ($msge as $key => $value) {
      //message recu
      if ($value["sender_id"] == $id_receiver) {
        $result .= '<div class="messageLeftReceive">' . $value["message"] . '<br><em>' . $value["date_pub"] . '</em></div>';
      }
      //message envoyé
      if ($value["sender_id"] == $id_sender) {
        $result .= '<div class="messageRightSend">' . $value["message"] . '<br><em>' . $value["date_pub"] . '</em></div>';
      }
    }
  }
  $result .= '
      </div>
      <div class="messageToSend">
        <form action="messages" method="POST">
          <textarea name="messageBody" rows="3" cols="54" placeholder="votre message" required></textarea>
          ';
  if (isset($id_sender)) {
    $result .= '<input type="hidden" name="id_sender" value="' . $id_sender . '"/>';
  } else {
    $result .= '<input type="hidden" name="id_sender" value=""/>';
  }
  if (isset($id_receiver)) {
    $result .= '<input type="hidden" name="id_receiver" value="' . $id_receiver . '"/>';
  } else {
    $result .= '<input type="hidden" name="id_receiver" value=""/>';
  }

  $result .= '
          <input type="submit" value="Send" name="sendMsgBt"/>
        </form>
      </div>
    </div>
    <div class="col-2">mess </div>
  </div>
  ';
  return $result;
}

function displayCreatemember()
{
  //redirection : protection
  if (!isset($_POST["createUserData"])) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }
  global $model;

  $result = '';
  if (isset($_POST["createUserData"])) {
    $createUserResult = $model->createUser($_POST["user_email"], $_POST["user_role"], $_POST["user_password"]);
    if ($createUserResult) {
      $result .= '
      <div class="d-grid gap-2">
      <button class="btn btn-success" type="button">
       Compte créé avec succes !
      </button>
      </div>
      ';
      //chargement des donnees membre
      $authentData = $model->authentifier($_POST["user_email"], $_POST["user_password"]);
      $_SESSION["user"] = [];
      foreach ($authentData as $key => $value) {
        $_SESSION["user"][$key] = $value;
      }
      // $result .= displayProfil();
    } else {
      $result .= '
      <div class="d-grid gap-2">
      <button class="btn btn-danger" type="button">
        erreur ! email existe !
      </button>
    </div>
      ';
    }
  }
  $result .= displayDashboard();
  return $result;
}
