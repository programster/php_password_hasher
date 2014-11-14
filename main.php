<?php


/**
 * Converts a raw password into a hash using PHP 5.5's new hashing method
 * @param String $raw_password - the password we wish to hash
 * @param int $cost - The two digit cost parameter is the base-2 logarithm of the 
 *                    iteration count for the underlying Blowfish-based hashing 
 *                    algorithmeter and must be in range 04-31
 * @return string - the generated hash
 */
function generate_password_hash($raw_password)
{
    $cost = 11;
    
    $options = [
        'cost' => $cost,
    ];

    $hash = password_hash($raw_password, PASSWORD_BCRYPT, $options);
    return $hash;
}

/**
 * Script function (not for websits) Fetches the password from the shell without it being 
 * displayed whilst being typed. Only works on *nix systems and requires shell_exec and stty.
 * 
 * @param stars - (optional) set to false to stop outputting stars as user types password. This 
 *                prevents onlookers seeing the password length but does make more difficult.
 * 
 * @return string - the password that was typed in. (any text entered before hitting return)
 */
function get_password_from_user_input($stars = true)
{
    // Get current style
    $oldStyle = shell_exec('stty -g');

    if ($stars === false) 
    {
        shell_exec('stty -echo');
        $password = rtrim(fgets(STDIN), "\n");
    } 
    else 
    {
        shell_exec('stty -icanon -echo min 1 time 0');

        $password = '';
        while (true) 
        {
            $char = fgetc(STDIN);

            if ($char === "\n") 
            {
                break;
            } 
            else if (ord($char) === 127) 
            {
                if (strlen($password) > 0) 
                {
                    fwrite(STDOUT, "\x08 \x08");
                    $password = substr($password, 0, -1);
                }
            } 
            else 
            {
                fwrite(STDOUT, "*");
                $password .= $char;
            }
        }
    }

    // Reset old style
    shell_exec('stty ' . $oldStyle);
    print PHP_EOL;

    // Return the password
    return $password;
}

$password1 = "a";
$password2 = "b";

while ($password1 !== $password2)
{
    print "Please enter the desired password" . PHP_EOL;
    $password1 = get_password_from_user_input();
    print "Please enter your password again to confirm:" . PHP_EOL;
    $password2 = get_password_from_user_input();

    if ($password1 !== $password2)
    {
        print "Sorry, passwords didnt match." . PHP_EOL;
    }
}

$password_hash = generate_password_hash($password1);
print "The hashed password is:" . PHP_EOL . $password_hash . PHP_EOL;
