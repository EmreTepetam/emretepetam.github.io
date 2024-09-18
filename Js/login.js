let incorrectLogin = 0;

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('loginForm');
  const captchaSection = document.getElementById('captchaSection');
  const captchaAnswer = document.getElementById('captchaAnswer');
  const errorMessage = document.getElementById('error-message');
  
  const captchaNumber1 = Math.floor(Math.random() * 10);
  const captchaNumber2 = Math.floor(Math.random() * 10);
  document.getElementById('captchaQuestion').textContent = `Kaç ${captchaNumber1} + ${captchaNumber2}?`;
  const correctCaptcha = captchaNumber1 + captchaNumber2;

  form.addEventListener('submit', function(event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const correctUsername = 'emre';
    const correctPassword = '12345';

    if (incorrectLogin >= 1) {
      const captchaInput = document.getElementById('captchaAnswer').value;
      if (parseInt(captchaInput) !== correctCaptcha) {
        errorMessage.textContent = 'Kullanıcı adı, şifre veya captcha hatalı!';
        errorMessage.style.display = 'block';
        return;
      }
    }

    if (username === correctUsername && password === correctPassword) {
      window.location.href = 'anasayfa.html';
    } else {
      incorrectLogin++;

      if (incorrectLogin === 1) {
        errorMessage.textContent = 'Kullanıcı adı veya şifre hatalı!';
      } else {
        errorMessage.textContent = 'Kullanıcı adı, şifre veya captcha hatalı!';
      }

      if (incorrectLogin >= 1) {
        captchaSection.style.display = 'block';
        captchaAnswer.setAttribute('required', '');
      }

      errorMessage.style.display = 'block';
    }
  });
});