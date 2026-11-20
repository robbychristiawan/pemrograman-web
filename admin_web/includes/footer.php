        <!-- footer -->
        <div class="container-fluid mt-4">
            <div class="footer text-center">
                <p>Copyright © <?= date('Y'); ?> Sistem Informasi Manajemen Rumah Sakit (SIMRS) - Powered by Pluto Template.<br>
                   Dibuat untuk Praktikum Pemrograman Web
                </p>
            </div>
        </div>
        <!-- end dashboard inner -->
    </div>
</div>
</div>
</div>

<!-- ============================================================== -->
<!-- MODAL POP-UP KONFIRMASI LOGOUT                                 -->
<!-- ============================================================== -->
<div class="modal fade" id="modalLogout" tabindex="-1" role="dialog" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold text-white" id="modalLogoutLabel">
                    <i class="fa fa-sign-out mr-2"></i> Konfirmasi Logout
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fa fa-sign-out fa-3x text-danger" style="transform: rotate(180deg);"></i>
                </div>
                <h5 class="font-weight-bold text-dark mb-2">Apakah Anda yakin ingin keluar dari sistem?</h5>
                <p class="text-muted mb-0">Sesi kerja Anda saat ini akan diakhiri dan Anda akan dialihkan kembali ke halaman login.</p>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal">Batal</button>
                <a href="logout.php" class="btn btn-danger font-weight-bold px-4">
                    <i class="fa fa-power-off mr-1"></i> Ya, Keluar Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!-- Nice scrollbar -->
<script src="js/perfect-scrollbar.min.js"></script>
<script>
    if (document.querySelector('#sidebar')) {
        var ps = new PerfectScrollbar('#sidebar');
    }
</script>
<!-- Custom js -->
<script src="js/custom.js"></script>
</body>
</html>
