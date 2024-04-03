function encryptPassword(password, key) {
    let encryptedPassword = "";
    for (let i = 0; i < password.length; i++) {
        encryptedPassword += String.fromCharCode(password.charCodeAt(i) ^ key.charCodeAt(i % key.length));
    }
    return encryptedPassword;
}