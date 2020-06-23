<?php

namespace WDA\Essentials;
use PDO,PDOException;

class Notes{
    
    public $db;
    protected $title = null;
    protected $id = null;
    protected $completed = false;
    public $db_host = "localhost";
    public $db_user = "root";
    public $db_pass = "";
    public $db_name = "mytodo";


    public function __construct()
    {
        try
        {
            $db = new PDO("mysql:host=" . $this->db_host . '; dbname=' . $this->db_name, $this->db_user, $this->db_pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
        $this->db = $db;
    }

    public function setId($id)
    {
        $this->id = $id;
        echo $id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setCompleted(){
        try{
            $stmt = $this->db->prepare("UPDATE tbltodo SET completed = '1' WHERE id=:id ");
            $stmt->bindparam(":id",$this->id);
            $stmt->execute();
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }

    }

    public function add(){
        try{
            $stmt = $this->db->prepare("INSERT INTO tbltodo(title) VALUES(:title)");
            $stmt->bindparam(":title",$this->title);
            $stmt->execute();
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }

    }

    public function get($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE id=:id");
        $stmt->execute(array(":id"=>$id));
        $editRow=$stmt->fetch(PDO::FETCH_ASSOC);
        return $editRow;
    }

    public function remove(){
        $stmt = $this->db->prepare("DELETE FROM tbltodo WHERE id=:id");
        $stmt->bindparam(":id",$this->id);
        $stmt->execute();
        return true;
    }
    
    public function clear_history(){
        $stmt = $this->db->prepare("DELETE FROM tbltodo WHERE completed='1' ");
        $stmt->execute();
        return true;
    }

    public function showAll()
    {
        ?>
        <div class="" id="showMain">
            <ul> 
                <?php
        
                $stmt = $this->db->prepare("SELECT * FROM tbltodo ORDER BY id DESC ");
                $stmt->execute();

                if($stmt->rowCount() >0){
                    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <li class="all-List" id="all-List">
                            <div class="row" id="list-contents">
                                <div class="col-md-2" id="list-check">
                                    <?php 
                                        $id =    $row["id"]; 
                                        $completed = $row["completed"];
                                        if($completed == 1)
                                        {
                                            ?> 
                                            <div class="container">
                                                <input class="checkbox" type="checkbox" value="'<?=$completed;?>'" checked disabled>
                                                <span class="checkmark"></span>
                                            </div>
                                            <?php
                                        } 
                                        else{
                                            ?> 
                                            <label class="container">
                                                <form class="form" method="post">
                                                    <input type="checkbox" name="completeTask" onchange="this.form.submit();" >
                                                    <input type="hidden" name="radio_id"  value="<?=$id?>">
                                                    <input type="hidden" name="radio_status"  value="<?=$completed;?>">
                                                    <span class="checkmark-in"></span>
                                                </form>
                                            </label>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <div class="col-md-8 text-left"  id="list-input">
                                    <?php 
                                        $title =    $row["title"]; 
                                        $id =    $row["id"]; 
                                        if($row['completed'] == 1)
                                        {   
                                            echo '
                                                <form method="post" class="row">
                                                    <div class="col-md-10">
                                                        <s> <h4> <input type="text" class="inputs-strike" name="task_title"    value="'. $row["title"].'"> </h4> </s>
                                                        <input type="hidden" name="task_id"    value="'. $id.'">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="submit" value= "'. $id.'" id= "'. $id.'"  class="btn-edit" name="btn-edit"></button>
                                                    </div>
                                                </form>        
                                            ';                         
                                        }
                                        else{
                                            echo '
                                                <form method="post" class="row">
                                                    <div class="col-md-10">
                                                        <h4> <input type="text" class="inputs" name="task_title" value="'. $row["title"].'"> </h4>
                                                        <input type="hidden" name="task_id"    value="'. $id.'">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="submit" value= "'. $id.'" 
                                                    id= "'. $id.'" class="btn-edit" name="btn-edit"></button>
                                                    </div>
                                                </form>
                                            ';  
                                        }
                                    ?>
                                </div>
                                <div class="col-md-2 text-right"  id="bDelete">
                                    <form method="post">
                                        <button type="submit" value= <?php print($row["id"]); ?> class="btn btn-primary" id="delete-button" name="btn-delete" > <i class="far fa-times-circle"></i> </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <hr style="background-color:white;">
                    <?php
                    }
                    ?>
                        <div class="row" id="nav-rows">
                            <div class="col-md-2">
                                <?php
                                $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE completed='0' ");
                                $stmt->execute();?>
                                    <h6><?=$stmt->rowCount()?> items left</h6>
                            </div>
                            <div class="col-md-1">
                                <form action="" method="post">
                                    <button type="submit" name="btn-all" id="allButton" class="btn btn-primary">All</button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form action="" method="post">
                                    <button type="submit" name="btn-completed" class="btn btn-primary" id="completed-button" ">Completed</button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form action="" method="post">
                                    <button type="submit" name="btn-incomplete" id="incomplete-button" class="btn btn-primary">Incomplete</button>
                                </form>
                            </div>
                        </div>
                    <?php
                }
                ?>
            </ul>
        </div>
        <?php
    }

    
    public function all()
    {
        ?>
        <div class="" id="all">
            <ul> 
                <?php
        
                $stmt = $this->db->prepare("SELECT * FROM tbltodo ORDER BY id DESC ");
                $stmt->execute();

                if($stmt->rowCount() >0){
                    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <li class="all-List" id="all-List">
                            <div class="row" id="list-contents">
                                <div class="col-md-2" id="list-check">
                                    <?php 
                                        $id =    $row["id"]; 
                                        $completed = $row["completed"];
                                        if($completed == 1)
                                        {
                                            ?> 
                                            <div class="container">
                                                <input class="checkbox" type="checkbox" value="'<?=$completed;?>'" checked disabled>
                                                <span class="checkmark"></span>
                                            </div>
                                            <?php
                                        } 
                                        else{
                                            ?> 
                                            <label class="container">
                                                <form class="form" method="post">
                                                    <input type="checkbox" name="completeTask" onchange="this.form.submit();" >
                                                    <input type="hidden" name="radio_id"  value="<?=$id?>">
                                                    <input type="hidden" name="radio_status"  value="<?=$completed;?>">
                                                    <span class="checkmark-in"></span>
                                                </form>
                                            </label>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <div class="col-md-8 text-left"  id="list-input">
                                    <?php 
                                        $title =    $row["title"]; 
                                        $id =    $row["id"]; 
                                        if($row['completed'] == 1)
                                        {   
                                            echo '
                                                <form method="post" class="row">
                                                    <div class="col-md-10">
                                                        <s> <h4> <input type="text" class="inputs-strike" name="task_title"    value="'. $row["title"].'"> </h4> </s>
                                                        <input type="hidden" name="task_id"    value="'. $id.'">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="submit" value= "'. $id.'" id= "'. $id.'"  class="btn-edit" name="btn-edit"></button>
                                                    </div>
                                                </form>        
                                            ';                         
                                        }
                                        else{
                                            echo '
                                                <form method="post" class="row">
                                                    <div class="col-md-10">
                                                        <h4> <input type="text" class="inputs" name="task_title" value="'. $row["title"].'"> </h4>
                                                        <input type="hidden" name="task_id"    value="'. $id.'">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="submit" value= "'. $id.'" 
                                                    id= "'. $id.'" class="btn-edit" name="btn-edit"></button>
                                                    </div>
                                                </form>
                                            ';  
                                        }
                                    ?>
                                </div>
                                <div class="col-md-2 text-right"  id="bDelete">
                                    <form method="post">
                                        <button type="submit" value= <?php print($row["id"]); ?> class="btn btn-primary" id="delete-button"  name="btn-delete" > <i class="far fa-times-circle"></i> </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <hr style="background-color:white;">
                    <?php
                    }
                    ?>
                        <div class="row" id="nav-rows">
                            <div class="col-md-2">
                                <?php
                                $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE completed='0' ");
                                $stmt->execute();?>
                                    <h6 id="left-items"><?=$stmt->rowCount()?> items left</h6>
                            </div>
                            <div class="col-md-2">
                                <form action="" method="post">
                                    <button type="submit" name="btn-all" id="allButton" class="btn btn-primary" onclick="showMe()">All</button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form action="" method="post">
                                    <button type="submit" name="btn-completed" class="btn btn-primary" id="completed-button">Completed</button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form action="" method="post">
                                    <button type="submit" name="btn-incomplete" id="incomplete-button" class="btn btn-primary">Incomplete</button>
                                </form>
                            </div>
                        </div>
                    <?php
                }
                ?>
            </ul>
        </div>
        <?php
    }

    public function completed()
    {
        ?>
        <div id="completed">
            <ul>
                <?php
                    $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE completed='1' ");
                    $stmt->execute();
                    if($stmt->rowCount() >0){
                        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                            $title =    $row["title"]; 
                            $id =    $row["id"]; 
                            $completed = $row["completed"];
                            ?>
                            <li class="all-List" id="list-completed">
                                <div class="row" id="list-contents">
                                    <div class="col-md-2" id="list-check">
                                        <?php  
                                            if($completed == 1)
                                            {
                                                ?> 
                                                <div class="container">
                                                    <input class="checkbox" type="checkbox" value="'<?=$completed;?>'" checked disabled>
                                                    <span class="checkmark"></span>
                                                </div>  
                                                <?php
                                            } 
                                            else{
                                                ?> 
                                                <label class="container">
                                                    <form class="form" method="post">
                                                        <input type="checkbox" name="completeTask" onchange="this.form.submit();" >
                                                        <input type="hidden" name="radio_id"  value="<?=$id?>">
                                                        <input type="hidden" name="radio_status"  value="<?=$completed;?>">
                                                        <span class="checkmark-in"></span>
                                                    </form>
                                                </label>
                                                <?php
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-8 text-left" id="list-input">
                            <?php 
                                $title =    $row["title"]; 
                                $id =    $row["id"]; 
                                if($row['completed'] == 1)
                                {   
                                    echo '
                                        <form method="post" class="row">
                                            <div class="col-md-10">
                                                <s> <h4> <input type="text" class="inputs-strike" name="task_title"    value="'. $row["title"].'"> </h4> </s>
                                                <input type="hidden" name="task_id"    value="'. $id.'">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" value= "'. $id.'" id= "'. $id.'"  class="btn-edit" name="btn-edit"></button>
                                            </div>
                                        </form>        
                                    ';                         
                                }
                                            ?>
                                    </div>
                                    <div class="col-md-2 text-right" id="bDelete">
                                        <form method="post">
                                            <button type="submit" value= <?php print($row["id"]); ?> class="btn btn-primary" name="btn-delete" id="delete-button" > <i class="far fa-times-circle"></i> </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            <hr style="background-color:white;">
                            <?php
                        }
                        ?>
                        <li class="all-List" id="list-completed">
                        <div class="row" id="nav-rows">
                            <div class="col-md-2">
                                <?php
                                $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE completed='0' ");
                                $stmt->execute();?>
                                    <h6 id="left-items"><?=$stmt->rowCount()?> items left</h6>
                            </div>
                            <div class="col-md-1">
                                <form action="" method="post">
                                    <button type="submit" name="btn-all" id="allButton" class="btn btn-primary">All</button>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <form action="" method="post">
                                    <button type="submit" name="btn-completed" class="btn btn-primary" id="completed-button" onclick="showMe('completed')">Completed</button>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <form action="" method="post">
                                    <button type="submit" name="btn-incomplete" id="incomplete-button" class="btn btn-primary" onclick="showMe('incomplete')">Incomplete</button>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <form action="" method="post">
                                    <button class="btn btn-danger" name="deleteCompleted" id="clear-history">Clear History</button>
                                </form>
                            </div>
                        </div>
                        </li>
                        <?php
                    }
                    else{                        
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h1 class="done">Nothing is completed!</h1>
                            </div>  
                        </div>
                        <br>
                        <div class="row" id="nav-rows">
                            <div class="col-md-3">
                                <?php
                                $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE completed='0' ");
                                $stmt->execute();?>
                                    <h6 id="left-items"><?=$stmt->rowCount()?> items left</h6>
                            </div>
                            <div class="col-md-2">
                                <form action="" method="post">
                                    <button type="submit" name="btn-all" id="allButton" class="btn btn-primary" onclick="showMe('all')">All</button>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <form action="" method="post">
                                    <button type="submit" name="btn-completed" class="btn btn-primary" id="completed-button" onclick="showMe('completed')">Completed</button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form action="" method="post">
                                    <button type="submit" name="btn-incomplete" id="incomplete-button" class="btn btn-primary" onclick="showMe('incomplete')">Incomplete</button>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </ul>
        </div>
        <?php
    }

    public function incomplete()
    {
        ?>
        <div class="" id="incomplete">
            <ul> 
                <?php
                    $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE completed='0' ");
                    $stmt->execute();
                    if($stmt->rowCount() >0){
                        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                            $title =    $row["title"]; 
                            $id =    $row["id"];                 
                            $completed = $row["completed"];
                            ?>
                            <li class="all-List" id="list-incomplete">
                                <div class="row" id="list-contents">
                                    <div class="col-md-2" id="list-check">
                                        <?php  
                                            if($completed == 1)
                                            {
                                                ?> 
                                                <div class="container">
                                                    <input class="checkbox" type="checkbox" value="'<?=$completed;?>'" checked disabled>
                                                    <span class="checkmark"></span>
                                                </div>
                                                <?php
                                            } 
                                            else{
                                                ?> 
                                                <label class="container">
                                                    <form class="form" method="post">
                                                        <input type="checkbox" name="completeTask" onchange="this.form.submit();" >
                                                        <input type="hidden" name="radio_id"  value="<?=$id?>">
                                                        <input type="hidden" name="radio_status"  value="<?=$completed;?>">
                                                        <span class="checkmark-in"></span>
                                                    </form>
                                                </label>
                                                <?php
                                            }
                                        ?>
                                    </div>    
                                    <div class="col-md-8">
                                        <?php 
                                            
                                            if($row['completed'] == 0)
                                            {
                                                echo '
                                                    <form method="post" class="row">
                                                        <div class="col-md-10">
                                                            <h4> <input type="text" class="inputs" name="task_title" value="'. $row["title"].'"> </h4>
                                                            <input type="hidden" name="task_id"    value="'. $id.'">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="submit" value= "'. $id.'" 
                                                        id= "'. $id.'" class="btn-edit" name="btn-edit"></button>
                                                        </div>
                                                    </form>
                                                ';  
                                            }
                                            ?>
                                    </div>
                                    <div class="col-md-2 text-right" id="bDelete">
                                        <form method="post">
                                            <button type="submit" value= <?php print($row["id"]); ?> class="btn btn-primary" name="btn-delete" id="delete-button" > <i class="far fa-times-circle"></i> </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            <hr style="background-color:white;">
                            <?php
                        }
                        ?>
                        <div class="row" id="nav-rows">
                            <div class="col-md">
                                <?php
                                $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE completed='0' ");
                                $stmt->execute();?>
                                    <h6 id="left-items"><?=$stmt->rowCount()?> items left</h6>
                            </div>
                            <div class="col-md">
                                <form action="" method="post">
                                    <button type="submit" name="btn-all" id="allButton" class="btn btn-primary" >All</button>
                                </form>
                            </div>
                            <div class="col-md">
                                <form action="" method="post">
                                    <button type="submit" name="btn-completed" class="btn btn-primary" id="completed-button">Completed</button>
                                </form>
                            </div>
                            <div class="col-md">
                                <form action="" method="post">
                                    <button type="submit" name="btn-incomplete" id="incomplete-button" class="btn btn-primary">Incomplete</button>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                    else{
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h1 class="done">All Done!</h1>
                            </div>  
                        </div>
                        <br><br>
                        <div class="row" id="nav-rows">
                            <div class="col-md-3" id="nav-count">
                                <?php
                                $stmt = $this->db->prepare("SELECT * FROM tbltodo WHERE completed='0' ");
                                $stmt->execute();?>
                                    <h6 id="left-items"><?=$stmt->rowCount()?> items left</h6>
                            </div>
                            <div class="col-md-2">
                                <form action="" method="post">
                                    <button type="submit" name="btn-all" class="btn btn-primary" id="allButton">All</button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form action="" method="post">
                                    <button type="submit" name="btn-completed" class="btn btn-primary" id="completed-button" >Completed</button>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <form action="" method="post">
                                    <button type="submit" name="btn-incomplete" id="incomplete-button" class="btn btn-primary" >Incomplete</button>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                
                ?>
            </ul>
        </div>
        <?php
    }

    public function complete(){
        try{
            $stmt = $this->db->prepare("UPDATE tbltodo SET completed='1'
                                        WHERE id=:id ");
            $stmt->bindparam(":id",$this->id);
            $stmt->execute();
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }

    }

    public function edit(){
        try{
            $stmt = $this->db->prepare("UPDATE tbltodo SET title =:title WHERE id=:id ");
            $stmt->bindparam(":id",$this->id);
            $stmt->bindparam(":title",$this->title);
            $stmt->execute();
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }

    }



}
