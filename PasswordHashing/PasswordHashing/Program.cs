using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace PasswordHashing
{
    class Program
    {
        static void Main(string[] args)
        {
            // ask user to login
            Console.Write("Create username:");
            String username = Console.ReadLine();
            Console.Write("Create password:");
            String password = Console.ReadLine();

            // save the hash with salt
            String hashWithSaltSavedInDatabase = GetMd5(password, username);
            Console.WriteLine("Hashes password with Salt saved to DB:" + hashWithSaltSavedInDatabase);

            // ask user to login again
            Console.Write("Login with username:");
            String username2 = Console.ReadLine();
            Console.Write("Login with password:");
            String password2 = Console.ReadLine();

            // determine hash with salt
            String hashedPasswordWithSalt = GetMd5(password2, username2);
            Console.WriteLine("2nd Hashed password with salt:" + hashedPasswordWithSalt);

            // compare 
            if (hashedPasswordWithSalt == hashWithSaltSavedInDatabase)
            {
                Console.WriteLine("Passwords matched!  Notice I did not save or compare passwords, just hashes");
                Console.WriteLine("Nate made pointless comment here for his github commit");
            }
            else
            {
                Console.WriteLine("Passwords did not match!  Notice I did not save or compare passwords, just hashes");
                Console.WriteLine("Please try again after sorting out your life.");
            }

            Console.ReadLine(); // pause before exit
            Console.ReadLine(); // pause extra long!
        }

        static string GetMd5(string password, string salt)
        {
            System.Security.Cryptography.MD5 md5 = System.Security.Cryptography.MD5.Create();

            byte[] inputBytes = System.Text.Encoding.ASCII.GetBytes(password + salt);

            byte[] hash = md5.ComputeHash(inputBytes);
            StringBuilder sb = new StringBuilder();
            for (int i = 0; i < hash.Length; i++)
            {
                sb.Append(hash[i].ToString());
            }
            return sb.ToString();
        }

    }
}