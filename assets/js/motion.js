(() => {
  const nav = document.querySelector('header nav');
  if (nav) for (const a of nav.querySelectorAll('a')) {
    const url = new URL(a.href, location.href);
    if (url.pathname === location.pathname || (location.pathname.endsWith('/') && url.pathname === location.pathname + 'index.php')) a.setAttribute('aria-current','page');
  }
  if (matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;
  const nodes = document.querySelectorAll('.hero > div:first-child, .page-header, .searchbar, .lot, .card, .auth-page, .page-container:not(.marketplace) > form, .page-container:not(.marketplace) > h1');
  const observer = new IntersectionObserver(entries => entries.forEach(entry => {
    if (entry.isIntersecting) {entry.target.classList.add('revealed');observer.unobserve(entry.target);}
  }), {threshold: .08});
  nodes.forEach((el,i) => {el.dataset.reveal='';el.style.setProperty('--reveal-delay',`${Math.min(i%4,3)*65}ms`);observer.observe(el);});
  document.documentElement.classList.add('motion-ready');
})();
