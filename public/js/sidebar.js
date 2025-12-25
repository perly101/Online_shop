// Sidebar functionality - reusable script for all pages
(function(){
  function initSidebar(){
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    
    function openSidebar(){
      if(sidebar) sidebar.classList.add('active');
      if(sidebarOverlay) sidebarOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar(){
      if(sidebar) sidebar.classList.remove('active');
      if(sidebarOverlay) sidebarOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
    
    if(menuBtn) menuBtn.addEventListener('click', openSidebar);
    if(sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if(sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
    
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape' && sidebar && sidebar.classList.contains('active')){
        closeSidebar();
      }
    });
  }
  
  // Run when DOM is ready
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', initSidebar);
  } else {
    initSidebar();
  }
})();
