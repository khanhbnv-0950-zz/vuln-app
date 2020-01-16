$(document).ready(
  $.each($('.btn-like'), (key, btn)=>{
    btn.addEventListener('click', (e)=>{
      e.preventDefault();
      var url = e.toElement.search;
      $.ajax({
        method: "GET",
        url: url,
        success: (data)=>{
          e.toElement.text = data+' Likes';
        }
      });
    });
  })
);