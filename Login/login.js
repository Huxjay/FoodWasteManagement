function showLogin(role) {
    document.getElementById('login-popup').classList.remove('hidden');
    document.getElementById('login-role').innerText = `${role} Login`;
  }
  
  function hideLogin() {
    document.getElementById('login-popup').classList.add('hidden');
  }
  