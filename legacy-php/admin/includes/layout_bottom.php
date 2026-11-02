    </main>
    <footer class="foot">© <?= date('Y') ?> e-Linx. All rights reserved.</footer>
  </div>
</div>

<div class="toasts" id="toasts" aria-live="polite"></div>

<div class="modal" id="confirmModal" hidden>
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
    <div class="modal-icon"><i class="bi bi-exclamation-triangle"></i></div>
    <h3 id="confirmTitle">Are you sure?</h3>
    <p id="confirmText"></p>
    <div class="modal-actions">
      <button type="button" class="btn btn-ghost" id="confirmNo">Cancel</button>
      <button type="button" class="btn btn-danger" id="confirmYes">Delete</button>
    </div>
  </div>
</div>

<script>window.__flashes = <?= json_encode(get_flashes(), JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
<script src="assets/js/admin.js"></script>
</body>
</html>
