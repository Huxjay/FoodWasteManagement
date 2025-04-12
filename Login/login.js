
// function showLogin(role) {
//   const popup = document.getElementById('login-popup');
//   const roleTitle = document.getElementById('login-role');

//   popup.classList.remove('hidden');
//   roleTitle.innerText = `${role} Login`;
// }

// function hideLogin() {
//   document.getElementById('login-popup').classList.add('hidden');
// }
function showLogin(role) {
  const popup = document.getElementById('login-popup');
  const roleTitle = document.getElementById('login-role');
  const roleInput = document.getElementById('user-role');

  popup.classList.remove('hidden');
  roleTitle.innerText = `${role} Login`;
  roleInput.value = role.toLowerCase(); // 👈 Set the hidden field
}

function hideLogin() {
  document.getElementById('login-popup').classList.add('hidden');
}
