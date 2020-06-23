<?php
    require('./vendor/autoload.php');
    ob_start();
    use WDA\Essentials\Notes;
    $toDo = new Notes();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>My Notes</title>
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/notepad.png" />

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/style.css">
    
    <!-- font awsome -->
    <script src="https://kit.fontawesome.com/82fb6fe7fe.js" crossorigin="anonymous"></script>
    
    <!-- Font Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
</head>
<body>
    <div class="body-section">
        <div class="container-fluid" id="section-body">
            <div class="container-fluid" id="title">
                <div class="row">
                    <div class="col-md-12" id="title-line">
                        <h1>ToDos</h1>
                    </div>
                </div>
            </div>
            <div class="row" id="section-row">
                <div class="col-md-4" id="row-col">
                    <form method="post" id="input-form">
                        <div class="form-group" id="forms">
                            <input type="text" name="task_title" id="task_input" placeholder="Add your task"><br>
                            <button type="submit" name="btn-save" class="btn btn-primary" id="btn-save">Submit</button>
                        </div>
                    </form>
                    <br>
                    <div class="content-section" >
                        <?php 
                            
                            if(isset($_POST['btn-save'])){
                                $taskTitle = $_POST['task_title'];
                                $toDo->setTitle($taskTitle);
                            
                                if($toDo->add())
                                {
                                    header("Location: index.php");
                                }
                                else
                                {
                                    header("Location: index.php?failure");
                                }
                            }
                            else if(isset($_POST['btn-complete'])){
                                $taskId = $_POST['btn-complete'];
                                $toDo->setId($taskId);
                                if($toDo->complete())
                                {
                                    header("Location: index.php");
                                }
                                else
                                {
                                    header("Location: index.php?notcompleted");
                                }
                            }
                            else if(isset($_POST['btn-delete'])){
                                $taskId = $_POST['btn-delete'];
                                $toDo->setId($taskId);
                                if($toDo->remove())
                                {
                                    header("Location: index.php");
                                }
                                else
                                {
                                    header("Location: index.php?notcompleted");
                                }
                            }
                            else if(isset($_POST['btn-edit']))
                            {
                                $title = $_POST['task_title'];
                                $id = $_POST['task_id'];
                                $toDo->setTitle($title);
                                $toDo->setId($id);
                                if($toDo->edit())
                                {
                                    header("Location: index.php");
                                }
                                else
                                {
                                    header("Location: index.php?failure");
                                }
                            }
                            else if(isset($_POST['btn-all']))
                            {
                                echo $toDo->all();
                                
                            }
                            else if(isset($_POST['btn-completed']))
                            {
                                $toDo->completed();
                            }
                            else if(isset($_POST['btn-incomplete']))
                            {
                                $toDo->incomplete();
                            }
                            else if(isset($_POST['completeTask']))
                            {
                                $task_done = $_POST['radio_status'];
                                if($task_done == 0)
                                {
                                    $id = $_POST['radio_id'];
                                    $toDo->setId($id);
                                    if($toDo->setCompleted())
                                    {
                                        header("Location: index.php");
                                    }
                                    else
                                    {
                                        header("Location: index.php?failure");
                                    }
                                }
                            }
                            else if(isset($_POST['deleteCompleted']))
                            {
                                if($toDo->clear_history())
                                {
                                    header("Location: index.php");
                                }
                                else
                                {
                                    header("Location: index.php?failure");
                                }
                            }
                            else{
                                $stmt = $toDo->db->prepare("SELECT * FROM tbltodo ORDER BY id DESC ");
                                $stmt->execute();

                                if($stmt->rowCount() >0){
                                    echo "<div>
                                            <ul> ";
                                                $toDo->showAll(); 
                                    echo "</ul>
                                            </div> ";
                                }
                            }
                        ?>                            
                    </div>          
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php ob_end_flush(); ?>





