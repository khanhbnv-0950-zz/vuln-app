<?php
foreach ($posts as $key=>$value) {
?>
<div class="media text-muted pt-3">
    <img class="rounded-circle mr-2" src="https://api.adorable.io/avatars/285/khanhact@adorable.png" alt=""
    style="height: 32px;">
    <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
    <span class="d-block text-gray-dark"><strong><?=$value['name']?></strong><span>&nbsp;<a href="./?page=profile&profile=<?=$value['username']?>">@<?=$value['username']?></span></a></span>
    <?=htmlspecialchars($value['content'])?>
    </p>
    <small class="ml-2">
    <a class="btn-like" href="./?api=like&id=<?=$value['post_id']?>"><?=$value['likes']?> Likes</a>
    <br>
    <a href="./?page=report&id=<?=$value['post_id']?>" class="text-muted">Report</a>
    </small>
</div>
<?php
}
?>