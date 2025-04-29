function showLogin(role) {
  const popup = document.getElementById('login-popup');
  const roleTitle = document.getElementById('login-role');
  const roleInput = document.getElementById('user-role');

  popup.classList.remove('hidden');
  roleTitle.innerText = `${role} Login`;
  roleInput.value = role.toLowerCase();
}

function hideLogin() {
  document.getElementById('login-popup').classList.add('hidden');
}
