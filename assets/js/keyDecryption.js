function decryptPassword(encryptedPassword, key) {
    let decryptedPassword = "";
    for (let i = 0; i < encryptedPassword.length; i++) {
        decryptedPassword += String.fromCharCode(encryptedPassword.charCodeAt(i) ^ key.charCodeAt(i % key.length));
    }
    return decryptedPassword;
}