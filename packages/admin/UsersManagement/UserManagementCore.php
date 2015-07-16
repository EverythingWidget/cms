<?php

function createGroup($title, $parentId, $description, $permission)
{
    if (!$title)
    {
        return false;
    }
    $row = mysql_num_rows(mysql_query("SELECT * FROM users_groups WHERE title = '$title' AND parent_id = '$parentId'"));
    if ($row > 0)
    {
        return 'duplicate';
    }
    $res = mysql_query("INSERT INTO users_groups (title , parent_id , description , permission) VALUES ('$title' , '$parentId' , '$description' , '$permission')");
    if ($res)
    {
        return mysql_insert_id();
    }
    return false;
}

function editGroup($title, $parentId, $description, $permission, $groupId)
{
    if (!$title || !$groupId)
    {
        return false;
    }
    return mysql_query("UPDATE users_groups SET title = '$title' 
            , parent_id = '$parentId' 
            , description = '$description'
            , permission = '$permission' 
             WHERE id = '$groupId'");
}

function getGroup($title)
{
    return mysql_fetch_array(mysql_query("SELECT * FROM users_groups WHERE title = '$title'"));
}

function getGroupsIds()
{
    $userName = $_SESSION['sesUserName'];
    $res = mysql_fetch_array(mysql_query("SELECT * FROM users,users_groups WHERE users.group_id = users_groups.id AND user_name = '$userName' LIMIT 1"));
    $groupPermissoins = $res['permission'];
    $groupPermissoins = split(',', $groupPermissoins);
    $index = 0;
    for ($i = 0; $i < count($groupPermissoins); $i++)
    {
        $temp = split(':', $groupPermissoins[$i]);
        if ($temp[0] == $sectionInfo['id'])
        {
            $permissions[$index++] = $temp[1];
        }
    }
    return $permissions;
}

function addUserToGroup($userId , $groupId)
{
    if(!$userId || !$groupId)
    {
        return false;
    }
    return mysql_query("UPDATE users SET group_id = CONCAT(group_id,'$groupId,') WHERE id = '$userId'");
}

?>
