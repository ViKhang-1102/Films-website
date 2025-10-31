<footer class="admin-footer bg-black border-top border-secondary mt-auto py-3">
  <div class="container-fluid d-flex justify-content-between text-secondary small">
    <span>&copy; <span id="year"></span> Fimls Admin</span>
    <span>v1.0.0</span>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('year').textContent = new Date().getFullYear();
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>{
  document.body.classList.toggle('sidebar-collapsed');
});
</script>
</body>
</html>

