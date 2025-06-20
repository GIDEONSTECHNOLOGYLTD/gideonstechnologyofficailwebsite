<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>User Profile</h1>
            <nav>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li><a href="/logout">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <main>
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success'] ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-container">
                <div class="profile-header">
                    <h2>Welcome, <?= $user->name ?></h2>
                    <p>Email: <?= $user->email ?></p>
                    <p>Member since: <?= date('F j, Y', strtotime($user->created_at)) ?></p>
                </div>
                
                <div class="profile-actions">
                    <a href="/profile/edit" class="btn btn-primary">Edit Profile</a>
                </div>
                
                <div class="profile-details">
                    <h3>Account Information</h3>
                    <table class="table">
                        <tr>
                            <th>Name:</th>
                            <td><?= $user->name ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?= $user->email ?></td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td><?= $user->role ?? 'User' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?= date('Y') ?> Your Application. All rights reserved.</p>
        </footer>
    </div>
    
    <script src="/assets/js/main.js"></script>
</body>
</html>