<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email Address</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px 20px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 28px; }
        .content { padding: 40px 30px; }
        .button { display: inline-block; padding: 15px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 20px 0; transition: transform 0.2s; }
        .button:hover { transform: translateY(-2px); }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e9ecef; }
        .welcome-icon { font-size: 48px; margin-bottom: 20px; }
        .features { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .feature-item { display: flex; align-items: center; margin: 10px 0; }
        .feature-icon { margin-right: 10px; font-size: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="welcome-icon">🎉</div>
            <h1>Welcome to {{ config('app.name') }}!</h1>
            <p style="color: #e8f4fd; margin: 10px 0 0 0; font-size: 18px;">Help reunite people with their belongings</p>
        </div>
        
        <div class="content">
            <h2 style="color: #333; margin-bottom: 20px;">Hi {{ $user->name }},</h2>
            
            <p style="font-size: 16px; margin-bottom: 20px;">
                Thank you for joining our Lost & Found community! We're excited to have you on board.
            </p>
            
            <p style="font-size: 16px; margin-bottom: 30px;">
                To get started and ensure the security of your account, please verify your email address by clicking the button below:
            </p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="button">
                    ✅ Verify Email Address
                </a>
            </div>
            
            <div class="features">
                <h3 style="color: #333; margin-bottom: 15px;">What you can do after verification:</h3>
                <div class="feature-item">
                    <span class="feature-icon">📝</span>
                    <span>Post lost or found items with photos</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🔍</span>
                    <span>Search and filter through community posts</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">💬</span>
                    <span>Message other users directly</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🎯</span>
                    <span>Claim found items that belong to you</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">📊</span>
                    <span>Track your posts and claims in your dashboard</span>
                </div>
            </div>
            
            <p style="font-size: 14px; color: #666; margin-top: 30px;">
                <strong>Security Note:</strong> This verification link will expire in 60 minutes for your security. 
                If you didn't create an account with us, please ignore this email.
            </p>
            
            <p style="font-size: 14px; color: #666;">
                If the button above doesn't work, you can copy and paste this link into your browser:
                <br>
                <a href="{{ $verificationUrl }}" style="color: #667eea; word-break: break-all;">{{ $verificationUrl }}</a>
            </p>
        </div>
        
        <div class="footer">
            <p style="margin: 0; color: #666; font-size: 14px;">
                <strong>{{ config('app.name') }}</strong> - Connecting communities, reuniting belongings
            </p>
            <p style="margin: 10px 0 0 0; color: #999; font-size: 12px;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>