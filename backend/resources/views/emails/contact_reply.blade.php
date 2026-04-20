<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>رد على رسالتك - {{ $storeName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333; direction: rtl; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1a73e8, #0d47a1); padding: 40px 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; font-weight: 700; margin-bottom: 6px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 14px; }
        .body { padding: 36px 32px; }
        .greeting { font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #1a1a2e; }
        .original-msg { background: #f8f9fa; border-right: 4px solid #1a73e8; border-radius: 6px; padding: 16px 20px; margin-bottom: 24px; }
        .original-msg .label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .original-msg p { font-size: 14px; color: #555; line-height: 1.7; }
        .reply-box { background: #e8f0fe; border-radius: 10px; padding: 24px; margin-bottom: 28px; }
        .reply-box .reply-label { font-size: 13px; font-weight: 700; color: #1a73e8; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
        .reply-box p { font-size: 15px; color: #1a1a2e; line-height: 1.8; white-space: pre-wrap; }
        .divider { border: none; border-top: 1px solid #eee; margin: 24px 0; }
        .cta { text-align: center; margin-bottom: 8px; }
        .cta a { display: inline-block; background: #1a73e8; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .footer { background: #f8f9fa; padding: 20px 32px; text-align: center; }
        .footer p { font-size: 12px; color: #aaa; line-height: 1.6; }
        .footer a { color: #1a73e8; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        <div class="header">
            <h1>{{ $storeName }}</h1>
            <p>رد على رسالتك من فريق الدعم</p>
        </div>

        <!-- Body -->
        <div class="body">
            <div class="greeting">مرحباً {{ $msg->name }}،</div>
            <p style="font-size:14px; color:#555; margin-bottom:24px; line-height:1.8;">
                شكراً لتواصلك معنا. يسعدنا الرد على استفسارك في أقرب وقت ممكن.
            </p>

            <!-- Original Message -->
            <div class="original-msg">
                <div class="label">رسالتك الأصلية</div>
                <p>{{ $msg->message }}</p>
            </div>

            <!-- Admin Reply -->
            <div class="reply-box">
                <div class="reply-label">
                    ✉️ رد فريق الدعم
                </div>
                <p>{{ $replyText }}</p>
            </div>

            <hr class="divider" />

            <div class="cta">
                <a href="{{ config('app.frontend_url', 'http://localhost:8080') }}/ContactPage">
                    تواصل معنا مجدداً
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                هذه الرسالة أُرسلت تلقائياً من متجر <strong>{{ $storeName }}</strong>.<br />
                إذا لم تتواصل معنا، يمكنك تجاهل هذه الرسالة.<br /><br />
                <a href="{{ config('app.frontend_url', 'http://localhost:8080') }}">زيارة المتجر</a>
            </p>
        </div>
    </div>
</body>
</html>
