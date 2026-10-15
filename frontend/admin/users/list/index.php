<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Member - Pustanka</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f7fc;
            color: #333;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: #0d3b66;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 0;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 0 20px 30px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-header img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .sidebar-header h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 25px;
            color: #c0d1e3;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-menu li.active a, 
        .sidebar-menu li a:hover {
            background-color: #092c4e;
            color: #ffffff;
            border-left: 4px solid #3182ce;
        }

        .sidebar-user {
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .sidebar-user:hover {
            background-color: #092c4e;
        }

        .sidebar-user img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .nav-profile {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-profile .bell-icon {
            font-size: 1.2rem;
            color: #4a5568;
            cursor: pointer;
        }

        .user-profile-box {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            text-decoration: none;
        }

        .user-profile-box img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #cbd5e0;
        }

        /* Container */
        .container {
            padding: 30px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0d3b66;
            margin-bottom: 25px;
        }

        /* Controls Section */
        .controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-table {
            position: relative;
            width: 380px;
        }

        .search-table input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border-radius: 8px;
            border: 1px solid #cbd5e0;
            outline: none;
            font-size: 0.88rem;
            background-color: #ffffff;
        }

        .search-table span {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .btn-add {
            background-color: #0d3b66;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-add:hover {
            background-color: #092c4e;
        }

        /* Table Card */
        .card-table {
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            padding: 16px 20px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #0d3b66;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 14px 20px;
            font-size: 0.88rem;
            border-bottom: 1px solid #edf2f7;
            color: #2d3748;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            background-color: #c6f6d5;
            color: #22543d;
            display: inline-block;
        }

        .badge-inactive {
            background-color: #fed7d7;
            color: #742a2a;
        }

        .action-btns {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            color: #4a5568;
            transition: color 0.2s;
        }

        .btn-action.edit:hover { color: #3182ce; }
        .btn-action.delete:hover { color: #e53e3e; }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
        }

        .page-btn {
            padding: 6px 12px;
            border: 1px solid #cbd5e0;
            background-color: #ffffff;
            color: #4a5568;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .page-btn.active {
            background-color: #0d3b66;
            color: #ffffff;
            border-color: #0d3b66;
        }

        /* Modal Popup */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0,0,0,0.4);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .modal-box {
            background-color: #ffffff;
            border-radius: 12px;
            width: 450px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            font-size: 1.2rem;
            color: #0d3b66;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #a0aec0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 9px 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e0;
            outline: none;
            font-size: 0.88rem;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #4a5568;
            padding: 9px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-submit-modal {
            background-color: #0d3b66;
            color: #ffffff;
            padding: 9px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div>
            <div class="sidebar-header">
                <img src="../../../../assets/images/logo-pustanka-nobg.png" alt="Logo" onerror="this.src='../../../../assets/images/logo-pustanka.png'">
                <h2>PUSTANKA</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="../../dashboard/"><span>🏠</span> Dashboard</a></li>
                <li><a href="../../books/list/"><span>📚</span> Katalog Buku</a></li>
                <li><a href="../../loans/"><span>📖</span> Peminjaman</a></li>
                <li><a href="../../fines/"><span>💳</span> Denda & Pembayaran</a></li>
                <li class="active"><a href="index.php"><span>👤</span> Member</a></li>
                <li><a href="../../reports/summary/"><span>📋</span> Laporan</a></li>
            </ul>
        </div>
        <!-- Link Klik Profil Kiri Bawah -->
        <a href="../../profile/" class="sidebar-user">
            <img src="https://ui-avatars.com/api/?name=Admin&background=random" id="sidebarAdminFoto" alt="Admin">
            <div>
                <h4 id="sidebarAdminNama" style="color:#fff; font-size:0.85rem;">Admin</h4>
                <p id="sidebarAdminEmail" style="color:#a0aec0; font-size:0.75rem;">admin@pustanka.id</p>
            </div>
        </a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- NAVBAR -->
        <div class="navbar">
            <div class="nav-profile">
                <div class="bell-icon">🔔</div>
                <!-- Link Klik Profil Kanan Atas -->
                <a href="../../profile/" class="user-profile-box">
                    <div style="text-align: right;">
                        <div style="font-size: 0.85rem; font-weight: 700; color: #2d3748;" id="navAdminNama">Admin</div>
                        <div style="font-size: 0.75rem; color: #718096;" id="navAdminEmail">admin@pustanka.id</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=random" id="navAdminFoto" alt="Admin">
                </a>
            </div>
        </div>

        <!-- CONTAINER -->
        <div class="container">
            <h1 class="page-title">Data Member</h1>

            <!-- CONTROLS ROW -->
            <div class="controls-row">
                <div class="search-table">
                    <span>🔍</span>
                    <input type="text" id="tableSearchInput" placeholder="Cari nama, nomor anggota, atau email">
                </div>
                <button class="btn-add" onclick="openAddModal()">+ Tambah Member</button>
            </div>

            <!-- TABLE CARD -->
            <div class="card-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama</th>
                            <th>No. Anggota</th>
                            <th>Email</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th style="width: 100px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="memberTableBody">
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px;">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="pagination-container">
                <button class="page-btn">&lt;</button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <span style="color: #a0aec0; font-size: 0.85rem;">...</span>
                <button class="page-btn">&gt;</button>
            </div>
        </div>
    </div>

    <!-- MODAL FORM TAMBAH / EDIT MEMBER -->
    <div class="modal-overlay" id="memberModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Member Baru</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="memberForm">
                <input type="hidden" id="editUserId">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" id="formNama" required placeholder="Masukkan nama member">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="formEmail" required placeholder="nama@email.com">
                </div>
                <div class="form-group" id="passwordGroup">
                    <label>Password</label>
                    <input type="password" id="formPassword" placeholder="Minimal 6 karakter">
                </div>
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" id="formNoTelp" placeholder="08123456789">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-submit-modal">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

<script>
    let allMembers = [];
    const API_BASE_URL = '../../../../api';

    function getAdminAvatarUrl(imagePath, name) {
        if (!imagePath || imagePath.trim() === '') {
            return `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'Admin')}&background=0D8ABC&color=fff`;
        }
        if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
            return imagePath;
        }
        const fileName = imagePath.split('/').pop();
        return `../../../../uploads/admins/${fileName}`;
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }

    async function checkAdminSession() {
        try {
            const res = await fetch(`${API_BASE_URL}/auth/admin_check.php`);
            const result = await res.json();
            if (!res.ok || result.status !== true) {
                window.location.href = '../../login/';
                return;
            }

            const adminData = result.data;
            document.getElementById('sidebarAdminNama').textContent = adminData.nama || 'Admin';
            document.getElementById('sidebarAdminEmail').textContent = adminData.email || 'admin@pustanka.id';
            document.getElementById('navAdminNama').textContent = adminData.nama || 'Admin';
            document.getElementById('navAdminEmail').textContent = adminData.email || 'admin@pustanka.id';

            const profileAvatar = getAdminAvatarUrl(adminData.foto, adminData.nama);
            document.getElementById('sidebarAdminFoto').src = profileAvatar;
            document.getElementById('navAdminFoto').src = profileAvatar;

            loadMembers();
        } catch (err) {
            window.location.href = '../../login/';
        }
    }

    async function loadMembers(query = '') {
        try {
            const res = await fetch(`${API_BASE_URL}/users/list.php?search=${encodeURIComponent(query)}`);
            const result = await res.json();

            const tbody = document.getElementById('memberTableBody');
            tbody.innerHTML = '';

            if (res.ok && result.status === true && result.data.length > 0) {
                allMembers = result.data;
                result.data.forEach((user, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${index + 1}</td>
                        <td style="font-weight: 600;">${user.nama}</td>
                        <td>${user.member_code}</td>
                        <td>${user.email}</td>
                        <td>${formatDate(user.created_at)}</td>
                        <td><span class="badge-status ${user.status !== 'aktif' ? 'badge-inactive' : ''}">${user.status ? user.status.charAt(0).toUpperCase() + user.status.slice(1) : 'Aktif'}</span></td>
                        <td style="text-align: center;">
                            <div class="action-btns" style="justify-content: center;">
                                <button class="btn-action edit" onclick="openEditModal(${user.id_user})" title="Edit">✏️</button>
                                <button class="btn-action delete" onclick="deleteMember(${user.id_user})" title="Hapus">🗑️</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #a0aec0;">Data member tidak ditemukan.</td></tr>';
            }
        } catch (err) {
            console.error('Error loading members:', err);
        }
    }

    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Member Baru';
        document.getElementById('editUserId').value = '';
        document.getElementById('memberForm').reset();
        document.getElementById('passwordGroup').style.display = 'block';
        document.getElementById('formPassword').required = true;
        document.getElementById('memberModal').style.display = 'flex';
    }

    function openEditModal(idUser) {
        const user = allMembers.find(m => m.id_user === idUser);
        if (!user) return;

        document.getElementById('modalTitle').textContent = 'Edit Data Member';
        document.getElementById('editUserId').value = user.id_user;
        document.getElementById('formNama').value = user.nama;
        document.getElementById('formEmail').value = user.email;
        document.getElementById('formNoTelp').value = user.no_telp || '';
        document.getElementById('passwordGroup').style.display = 'none';
        document.getElementById('formPassword').required = false;
        document.getElementById('memberModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('memberModal').style.display = 'none';
    }

    document.getElementById('memberForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const idUser = document.getElementById('editUserId').value;
        const nama = document.getElementById('formNama').value.trim();
        const email = document.getElementById('formEmail').value.trim();
        const password = document.getElementById('formPassword').value;
        const noTelp = document.getElementById('formNoTelp').value.trim();

        const isEdit = idUser !== '';
        const targetUrl = isEdit ? `${API_BASE_URL}/users/update.php` : `${API_BASE_URL}/users/create.php`;

        const payload = isEdit 
            ? { id_user: parseInt(idUser), nama: nama, email: email, no_telp: noTelp }
            : { nama: nama, email: email, password: password, no_telp: noTelp };

        try {
            const res = await fetch(targetUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await res.json();

            if (res.ok && result.status === true) {
                alert(result.message);
                closeModal();
                loadMembers();
            } else {
                alert(result.message || 'Gagal menyimpan data.');
            }
        } catch (err) {
            console.error('Submit error:', err);
            alert('Gagal terhubung ke API: ' + targetUrl);
        }
    });

    async function deleteMember(idUser) {
        if (!confirm('Apakah Anda yakin ingin menghapus member ini?')) return;

        try {
            const res = await fetch(`${API_BASE_URL}/users/delete.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_user: parseInt(idUser) })
            });

            const result = await res.json();

            if (res.ok && result.status === true) {
                alert(result.message);
                loadMembers();
            } else {
                alert(result.message || 'Gagal menghapus member.');
            }
        } catch (err) {
            console.error('Delete error:', err);
            alert('Gagal terhubung ke API Hapus.');
        }
    }

    document.getElementById('tableSearchInput').addEventListener('input', function(e) {
        loadMembers(e.target.value);
    });

    document.addEventListener('DOMContentLoaded', () => {
        checkAdminSession();
    });
</script>

</body>
</html>