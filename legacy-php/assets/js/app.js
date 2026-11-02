$(function(){
  $('a[href^="#"]').on('click',function(e){const id=$(this).attr('href');if(id.length>1&&$(id).length){e.preventDefault();$('html,body').animate({scrollTop:$(id).offset().top-85},500);}});
  $('.counter').each(function(){const $this=$(this);$this.prop('Counter',0).animate({Counter:$this.data('count')},{duration:1800,easing:'swing',step:function(now){$this.text(Math.ceil(now));}});});
  setTimeout(()=>$('.alert').fadeOut(600),5000);
});
