<?php
$hastag = "#";

if (!isset($post['tagidlist'])) {
    if (!isset($_GET['tag_id'])) {
        $hastag = " ";
    }else{
     $post['tagidlist'] = intval($_GET['tag_id']);
    }
}

?>
<?php $explodeTag = explode(",", $post['taglist']) ?>
<?php $explodeTagId = explode(",", $post['tagidlist']);?>

<?php


while ($explodeTag) {

    $curentTag = array_shift($explodeTag);
    $curentTagId = array_shift($explodeTagId);
    ?>

    <a href="../pages/tags.php?tag_id=<?php echo $curentTagId ?>"><?php echo $hastag, $curentTag ?></a>
    <?php

}
?>