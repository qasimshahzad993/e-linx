$(function(){
  $('a[href^="#"]').on('click',function(e){const id=$(this).attr('href');if(id.length>1&&$(id).length){e.preventDefault();$('html,body').animate({scrollTop:$(id).offset().top-85},500);}});
  $('.counter').each(function(){const $this=$(this);$this.prop('Counter',0).animate({Counter:$this.data('count')},{duration:1800,easing:'swing',step:function(now){$this.text(Math.ceil(now));}});});
  setTimeout(()=>$('.alert').fadeOut(600),5000);

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!reducedMotion) {
    document.querySelectorAll('[data-rotating-phrases]').forEach((element) => {
      const phrases = JSON.parse(element.dataset.rotatingPhrases);

      if (!Array.isArray(phrases) || phrases.length < 2 || phrases.some((phrase) => typeof phrase !== 'string')) return;

      let index = Math.max(0, phrases.indexOf(element.textContent.trim()));
      const rotation = window.matchMedia('(max-width: 767px)').matches ? 5600 : 4600;

      window.setInterval(() => {
        if (document.hidden) return;

        element.classList.add('is-changing');

        window.setTimeout(() => {
          index = (index + 1) % phrases.length;
          element.textContent = phrases[index];
          element.classList.remove('is-changing');
        }, 260);
      }, rotation);
    });
  }

  const cursor = document.querySelector('.elinx-cursor');
  const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  if (cursor && finePointer && !reducedMotion) {
    document.body.classList.add('has-elinx-cursor');

    let pointerX = 0;
    let pointerY = 0;
    let framePending = false;

    document.addEventListener('pointermove', (event) => {
      pointerX = event.clientX;
      pointerY = event.clientY;

      if (framePending) return;
      framePending = true;

      window.requestAnimationFrame(() => {
        cursor.style.transform = `translate3d(${pointerX}px, ${pointerY}px, 0) translate(-50%, -50%)`;
        cursor.classList.add('is-visible');
        framePending = false;
      });
    }, { passive: true });

    document.addEventListener('pointerdown', () => cursor.classList.add('is-pressed'));
    document.addEventListener('pointerup', () => cursor.classList.remove('is-pressed'));
    document.addEventListener('pointerleave', () => cursor.classList.remove('is-visible'));
    window.addEventListener('blur', () => cursor.classList.remove('is-visible', 'is-pressed'));

    document.addEventListener('pointerover', (event) => {
      if (event.target instanceof Element && event.target.closest('a, button, [role="button"]')) {
        cursor.classList.add('is-hovering');
      }
    });

    document.addEventListener('pointerout', (event) => {
      if (!(event.target instanceof Element)) return;

      const interactive = event.target.closest('a, button, [role="button"]');
      const next = event.relatedTarget instanceof Element
        ? event.relatedTarget.closest('a, button, [role="button"]')
        : null;

      if (interactive && interactive !== next) cursor.classList.remove('is-hovering');
    });
  }
});
