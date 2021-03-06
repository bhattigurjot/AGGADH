<?php require 'db.php';

function getProfileid() {
    $pi = uniqid();
    $pis = hexdec($pi);
    $len = strlen($pis);
    $st = $len - 8;
    $ret_id = substr($pis, $st);
    return $ret_id;
}

function getRowsFromEmail($email)
{
    $getquery = "Select * from users_reg WHERE `email`= ? ;";
    global $conn;
                        $q = $conn->prepare($getquery);
                        if ($q === FALSE) {
                            trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                        }
                        $q->bind_param('s', $em);
                        $em = $email;
                        $q->execute();
                        $q->store_result();
                        $rows = $q->num_rows;
                        $q->close();
                        
                        return $rows;
}
function regNewUser($em, $psd)
{
    global $conn;
    $insquery = "INSERT INTO users_reg(`email`,`password`,`profile_id`,`confirm_code`) VALUES(?,?,?,?)";
                            $qs = $conn->prepare($insquery);

                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $qs->bind_param('ssis', $ems, $pass, $pid, $cc);
                            $ems = $em;
                            $pass = sha1($psd) . md5($psd);
                            $pid = getProfileid();
                            $cc = md5($em);
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();
                            
                            return $rows;
}

function adminLogin($username, $password)
{
    global $conn;
    $query = "SELECT a_id from admins WHERE `a_username`=? AND `a_password`=? ;";
                            $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $q->bind_param('ss', $userna,$passwo);
                        $userna = $username;
                        $passwo =$password;
                        $q->execute();
                        $q->bind_result($admin_id);
                        $q->fetch();
                        $q->close();                                            
                        return $admin_id;
                        
}

function adminName($a_id)
{
    global $conn;
    $query = "SELECT a_name from admins WHERE `a_id`=? ;";
                            $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $q->bind_param('i', $aid);
                        $aid=$a_id;
                        $q->execute();
                        $q->bind_result($admin_name);
                        $q->fetch();
                        $q->close();                                            
                        return $admin_name;
                        
}

function addBranch($branchName, $branchSlug)
{
    global $conn;
    $insquery = "INSERT INTO branches(`branch_name`,`bn_slug`) VALUES(?,?)";
                            $qs = $conn->prepare($insquery);

                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $qs->bind_param('ss', $bnn, $bns);
                            $bnn = $branchName;
                            $bns = $branchSlug;
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();
                            
                            return $rows;
}


function getBranches()
{
    $getquery = "Select * from branches ;";
    global $conn;
                        $res = $conn->query($getquery);
                     
                     return $res;
}

function getBranchInfo($id)
{
    
    global $conn;
    $query = "SELECT branch_name, bn_slug from branches WHERE `branch_id`=? ;";
                            $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $q->bind_param('s', $bid);
                        $bid = $id;
                        $q->execute();
                        $q->bind_result($b_name,$b_slug);
                        $q->fetch();
                        $q->close();                       
                        $row[0] = $b_name;
                        $row[1] = $b_slug;
                        return $row;
    
}

function updateBranch($branchName, $branchSlug, $id)
{
    global $conn;
    $insquery = "UPDATE branches SET `branch_name`=?,`bn_slug`=? WHERE `branch_id` = ?";
                            $qs = $conn->prepare($insquery);

                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $qs->bind_param('ssi', $bnn, $bns, $bid);
                            $bnn = $branchName;
                            $bns = $branchSlug;
                            $bid = $id;
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();
                            
                            return $rows;
}


function deleteBranch($id)
{
    global $conn;
    $insquery = "DELETE FROM branches WHERE `branch_id` = ?";
                            $qs = $conn->prepare($insquery);
                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            $qs->bind_param('i', $bid);                            
                            $bid = $id;
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();                            
                            return $rows;
}


function addSubject($subjectName, $subjectSlug)
{
    global $conn;
    $insquery = "INSERT INTO subjects(`sub_name`,`sub_slug`) VALUES(?,?)";
                            $qs = $conn->prepare($insquery);

                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $qs->bind_param('ss', $bnn, $bns);
                            $bnn = $subjectName;
                            $bns = $subjectSlug;
                            $qs->execute();
                            $idd = $qs->insert_id;
                            $qs->close();
                            addSubjectsTable($subjectSlug);
                            return $idd;
}

function addSubjectsTable($subSlug)
{
    global $conn;
    $query = "CREATE TABLE IF NOT EXISTS `quest_$subSlug` (
  `quest_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `quest` text NOT NULL,
  `op_a` text NOT NULL,
  `op_b` text NOT NULL,
  `op_c` text NOT NULL,
  `op_d` text NOT NULL,
  `c_ans` char(1) NOT NULL,
  `level_id` int(11) NOT NULL,
  PRIMARY KEY (`quest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
    
    $q = $conn->query($query); 
}

function modifyUserTable($uname)
{global $conn;
    $query2="ALTER TABLE `user_$uname`
  ADD CONSTRAINT `user_'$uname'_ibfk_2` FOREIGN KEY (`level`) REFERENCES `level` (`level_id`),
  ADD CONSTRAINT `user_'$uname'_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`topic_id`);";
    
    $qs = $conn->query($query2); 

}

function addUserTable($uname){
    global $conn;
    $query = "CREATE TABLE IF NOT EXISTS `user_$uname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `marks` int(11) NOT NULL,
  `attempts` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`,`level`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    
    $q = $conn->query($query); 
    
    modifyUserTable($uname);
}



function delSubjectsTable($subSlug)
{
    global $conn;
    $query = "DROP TABLE IF EXISTS `quest_$subSlug`;";
    
    $q = $conn->query($query); 
}


function mapSubjectToBranches($branch_id,$subject_id)
{
    global $conn;
    $query = "INSERT INTO branch_sub_map VALUES (NULL,?,?)";
    $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("ii",$bid,$sid);
                            $i=0;
                            foreach ($branch_id as $bidd) {
                                
                                $bid=$bidd;
                                $sid=$subject_id;
                                $q->execute();
                                $r = $q->affected_rows;
                                if($r>0)
                                {
                                    $i++;
                                }                     
                            }
                            $q->close();
                            return $i;
    
}


function getSubjects()
{
    $getquery = "Select * from subjects ;";
    global $conn;
                        $res = $conn->query($getquery);
                     
                     return $res;
}

function getBranchIdsForSubject($sub_id)
{
    $query = "Select branch_id FROM branch_sub_map WHERE `sub_id` = ?";
     global $conn;
     
     $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("i",$sid);
                            $sid = $sub_id;
                            $q->execute();
                            $q->bind_result($branch);
                            while($q->fetch())
                            {
                                $branches[] = $branch;
                            }
                            return $branches;
}


function deleteSubjectMap($id)
{
    global $conn;
    $insquery = "DELETE FROM branch_sub_map WHERE `sub_id` = ?";
                            $qs = $conn->prepare($insquery);
                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            $qs->bind_param('i', $bid);                            
                            $bid = $id;
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();                            
                            return $rows;
}

function deleteSubject($id)
{
    global $conn;
    $insquery = "DELETE FROM subjects WHERE `sub_id` = ?";
                            $qs = $conn->prepare($insquery);
                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            $qs->bind_param('i', $bid);                            
                            $bid = $id;
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();                            
                            return $rows;
}

function getTopics()
{
    $getquery = "Select * from topics ;";
    global $conn;
                        $res = $conn->query($getquery);
                     
                     return $res;
}

function addTopic($topicName, $topicSlug)
{
     global $conn;
    $insquery = "INSERT INTO topics(`topic_name`,`topic_slug`) VALUES(?,?)";
                            $qs = $conn->prepare($insquery);

                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $qs->bind_param('ss', $bnn, $bns);
                            $bnn = $topicName;
                            $bns = $topicSlug;
                            $qs->execute();
                            $idd = $qs->insert_id;
                            $qs->close();
                            return $idd;
}

function mapTopicToSubjects($topic_id,$subject_id)
{
    global $conn;
    $query = "INSERT INTO sub_topic_map VALUES (NULL,?,?)";
    $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("ii",$sid,$tid);
                                                      
                                $sid=$subject_id;
                                $tid=$topic_id;
                                $q->execute();
                                $r = $q->affected_rows;
                                
                            $q->close();
                            return $r;
    
}

function getSubjectIdsForTopics($sub_id)
{
    $query = "Select sub_id FROM sub_topic_map WHERE `topic_id` = ?";
     global $conn;
     
     $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("i",$sid);
                            $sid = $sub_id;
                            $q->execute();
                            $q->bind_result($topics);
                            while($q->fetch())
                            {
                                $topics_id[] = $topics;
                            }
                            return $topics_id;
}

function getSubjectInfo($id)
{
    
    global $conn;
    $query = "SELECT sub_name, sub_slug from subjects WHERE `sub_id`=? ;";
                            $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $q->bind_param('s', $bid);
                        $bid = $id;
                        $q->execute();
                        $q->bind_result($b_name,$b_slug);
                        $q->fetch();
                        $q->close();                       
                        $row[0] = $b_name;
                        $row[1] = $b_slug;
                        return $row;
    
}

function addLevel($levelName)
{
    global $conn;
    $insquery = "INSERT INTO level(`level_name`) VALUES(?)";
                            $qs = $conn->prepare($insquery);

                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $qs->bind_param('s', $lnn);
                            $lnn = $levelName;
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();
                            
                            return $rows;
}

function getLevels()
{
    $getquery = "Select * from level ;";
    global $conn;
                        $res = $conn->query($getquery);
                     
                     return $res;
}

function deleteLevel($id)
{
    global $conn;
    $insquery = "DELETE FROM level WHERE `level_id` = ?";
                            $qs = $conn->prepare($insquery);
                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            $qs->bind_param('i', $bid);                            
                            $bid = $id;
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();                            
                            return $rows;
}

function addQuestion($sub_slug,$ques,$opt_a,$opt_b,$opt_c,$opt_d,$corr_opt,$ques_level,$topics)
{
    global $conn;
    $insquery = "INSERT INTO quest_$sub_slug VALUES(NULL,?,?,?,?,?,?,?,?)";
                            $qs = $conn->prepare($insquery);

                            if ($qs === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $qs->bind_param('issssssi',$tid,$q,$a,$b,$c,$d,$cc,$lid);
                            $tid = $topics;
                            $q = $ques;
                            $a = $opt_a;
                            $b = $opt_b;
                            $c = $opt_c;
                            $d = $opt_d;
                            $cc = $corr_opt;
                            $lid = $ques_level;
                            $qs->execute();
                            $rows = $qs->affected_rows;
                            $qs->close();
                            
                            return $rows;
}



function getTopicForSubject($sub_id)
{
     global $conn;
     
     $getquery = "Select * from topics WHERE topic_id IN (Select topic_id FROM sub_topic_map WHERE sub_id = '$sub_id');";
                        $res = $conn->query($getquery);
                    
                     return $res;                                                                       
}


function getTopicInfo($id)
{
    
    global $conn;
    $query = "SELECT topic_name, topic_slug from topics WHERE `topic_id`=? ;";
                            $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $q->bind_param('i', $bid);
                        $bid = $id;
                        $q->execute();
                        $q->bind_result($b_name,$b_slug);
                        $q->fetch();
                        $q->close();                       
                        $row[0] = $b_name;
                        $row[1] = $b_slug;
                        return $row;
    
}

function getLevelInfo($id)
{
    
    global $conn;
    $query = "SELECT level_name from level WHERE `level_id`=? ;";
                            $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $q->bind_param('i', $bid);
                        $bid = $id;
                        $q->execute();
                        $q->bind_result($b_name);
                        $q->fetch();
                        $q->close();                       
                        return  $b_name;   
}


function getQuestions($sub_slug)
{
    
    $getquery = "Select * from quest_$sub_slug ;";
    global $conn;
                        $res = $conn->query($getquery);
                     
                     return $res;
    
}


function getQuestionsOfTopicAndLevel($sub_slug,$topic_id,$level_id)
{
    
    $getquery = "Select * from quest_$sub_slug WHERE `topic_id` = '$topic_id' AND `level_id` = '$level_id';";
    global $conn;
                        $res = $conn->query($getquery);
                     
                     return $res;
    
}

function getUserInfo($profile_id)
{
    
    $query = "Select name,dob,contact_no,sex,user_name,branch_id from users_info WHERE `profile_id`=? ;";
    global $conn;
                        $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }

                            $q->bind_param('i', $pid);
                        $pid = $profile_id;
                        $q->execute();
                        $q->bind_result($name,$dob,$cno,$sex,$uname,$branchid);
                        $q->fetch();
                        $q->close();
                        $userInfo['name']=$name;
                        $userInfo['dob']=$dob;
                        $userInfo['contact_no']=$cno;
                        $userInfo['sex']=$sex;
                        $userInfo['user_name']=$uname;
                        $userInfo['branch_id']=$branchid;
                        
                        return $userInfo;
    
}


function getSubjectIdsForBranch($b_id)
{
    $query = "Select sub_id FROM branch_sub_map WHERE `branch_id` = ?";
     global $conn;
     
     $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("i",$sid);
                            $sid = $b_id;
                            $q->execute();
                            $q->bind_result($subs);
                            while($q->fetch())
                            {
                                $subjects[] = $subs;
                            }
                            return $subjects;
}


function getUserTestInfo($uname,$topic_id)
{
    $query = "Select marks, attempts, level FROM user_$uname WHERE `topic_id` = ?";
     global $conn;
     
     $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("i",$sid);
                            $sid = $topic_id;
                            $q->execute();
                            $q->bind_result($marks,$attempts,$level);
                            $q->fetch();
                            $info['marks']=$marks;
                            $info['attempts'] = $attempts;
                            $info['level'] = $level;
                            $q->close();
                            return $info;
}

function getUserLevel($uname,$sub_id)
{
     $query = "Select max(level) FROM `user_$uname` WHERE `topic_id` IN (Select topic_id FROM sub_topic_map WHERE `sub_id` = ?)";
     global $conn;
     
     $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("i",$sid);
                            $sid = $sub_id;
                            $q->execute();
                            $q->bind_result($level); 
                            $q->fetch();
                            $q->close();
                            return $level;
}

function getAttemptsAndMarks($un, $topic_id, $level_id)
{
    $query = "Select attempts, marks FROM `user_$un` WHERE `topic_id` = ? AND `level` = ?";
     global $conn;
     
     $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("ii",$sid,$lid);
                            $sid = $topic_id;
                            $lid = $level_id;
                            $q->execute();
                            $q->bind_result($at,$mark); 
                            $q->fetch();
                            $ret['attempts'] = $at;
                            $ret['marks'] = $mark;
                            $q->close();
                            return $ret;
}

function updateAttemptsAndMarks($un, $topic_id, $level_id,$marks,$att)
{
    $query = "Update `user_$un` SET `attempts` = ? , `marks` = ? WHERE `topic_id` = ? AND `level` = ?";
     global $conn;
     
     $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("iiii",$attem,$mrks,$sid,$lid);
                            $attem =$att;
                            $mrks = $marks;
                            $sid = $topic_id;
                            $lid = $level_id;
                            $q->execute();
                            $ret = $q->affected_rows;
                            $q->close();
                            return $ret;
}


function addAttemptsAndMarks($un, $topic_id, $level_id,$marks,$att)
{
    $query = "Insert into `user_$un` values(null, ?, ?, ? ,?)";
     global $conn;
     
     $q = $conn->prepare($query);

                            if ($q === FALSE) {
                                trigger_error('Error: ' . $conn->error, E_USER_ERROR);
                            }
                            
                            $q->bind_param("iiii",$topi,$mrks,$attem,$lid);
                            $topi = $topic_id;
                            $mrks = $marks;
                            $attem =$att;
                            $lid = $level_id;
                            $q->execute();
                            $ret = $q->affected_rows;
                            $q->close();
                            return $ret;
}


