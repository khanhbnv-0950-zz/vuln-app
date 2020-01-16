$(document).ready(
$("#btn-follow").click((e)=>{
    e.preventDefault();
    var url = e.toElement.search;
    arr = url.split('action=');
    user = arr[1].split('user=');
    $.ajax({
        url: url,
        success: (data)=>{
            arr1 = data.split('|');
            e.toElement.text = arr1[0];
            e.toElement.search = arr[0]+'action='+arr1[1]+'&user='+user[1];
        }
    });
}));
$(document).ready(
$('.btn-approve, .btn-reject').click((e)=>{actions(e)}));
function actions(e) {
    e.preventDefault();
    btn = $(e.target);
    url = btn.attr('href');
    $.ajax({
        url: url,
        success: (data)=>{
            if (data == 'true') {
                btn.parent().parent().remove();
            } else {
                alert('Không thành công');
            }
        }
    });
}
