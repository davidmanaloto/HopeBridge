package com.example.hopebridge

import android.animation.ObjectAnimator
import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity

class Homepage : AppCompatActivity() {
    private var isMenuOpen = false
    private lateinit var sharedPreferences: SharedPreferences

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_homepage)

        // Initialize sharedPreferences
        sharedPreferences = getSharedPreferences("UserSession", MODE_PRIVATE)



        val username = sharedPreferences.getString("username", "Guest") ?: "Guest"
        findViewById<TextView>(R.id.welcome).text = "Welcome $username"

        val hamburger: View = findViewById(R.id.hamburger)
        val burgers: View = findViewById(R.id.burgers)
        val profileSection: View = findViewById(R.id.profile_section)
        val logoutSection: View = findViewById(R.id.logout_section)
        val orgsButton: Button = findViewById(R.id.orgs)
        val aboutSection: View = findViewById(R.id.about_section)
        val userpost: Button = findViewById(R.id.userpost)

        burgers.translationX = -900f

        hamburger.setOnClickListener {
            val targetX = if (isMenuOpen) -900f else 0f
            ObjectAnimator.ofFloat(burgers, "translationX", targetX).setDuration(100).start()
            isMenuOpen = !isMenuOpen
        }

        profileSection.setOnClickListener {
            val intent = Intent(this, UserInfo::class.java)
            startActivity(intent)
        }


        orgsButton.setOnClickListener {
            val intent = Intent(this, Organization::class.java)
            startActivity(intent)
        }

        aboutSection.setOnClickListener {
            val intent = Intent(this, About::class.java)
            startActivity(intent)
        }

        logoutSection.setOnClickListener {
            logout()
        }

        userpost.setOnClickListener {
            val intent = Intent(this, UserPost::class.java)
            startActivity(intent)
        }
    }

    private fun logout() {
        val editor = sharedPreferences.edit()
        editor.clear() // Clears all saved session data
        editor.apply()

        // Redirect to login screen
        val intent = Intent(this, MainActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK // Clears back stack
        startActivity(intent)
        finish()

        Toast.makeText(this, "Logged out successfully", Toast.LENGTH_SHORT).show()
    }



}


