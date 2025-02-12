const loginBox = document.getElementById('login-box');
const signupBox = document.getElementById('signup-box');


const showSignupLink = document.getElementById('show-signup');
const showLoginLink = document.getElementById('show-login');


showSignupLink.addEventListener('click', (e) => {
    e.preventDefault();  
    loginBox.style.display = 'none';  
    signupBox.style.display = 'block';  
});


showLoginLink.addEventListener('click', (e) => {
    e.preventDefault();  
    signupBox.style.display = 'none';  
    loginBox.style.display = 'block';  
});
