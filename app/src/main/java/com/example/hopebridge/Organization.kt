package com.example.hopebridge

import android.animation.ObjectAnimator
import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.TextView
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat

class Organization : AppCompatActivity() {

    private var isMenuOpen = false
    private lateinit var sharedPreferences: SharedPreferences

    override fun onCreate(savedInstanceState: Bundle?) {

        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_organization)

        val hamburger: View = findViewById(R.id.hamburger)
        val burgers: View = findViewById(R.id.burgers)
        val profileSection: View = findViewById(R.id.profile_section)
        val logoutSection: View = findViewById(R.id.logout_section)
        val newsButton: Button = findViewById(R.id.news)
        val aboutSection: View = findViewById(R.id.about_section)
        val projectSection: View = findViewById(R.id.project_section)
        val userpost: Button = findViewById(R.id.userpost)

        sharedPreferences = getSharedPreferences("userPrefs", MODE_PRIVATE)
        val username = sharedPreferences.getString("username", "Guest") ?: "Guest"
        findViewById<TextView>(R.id.welcome).text = "Welcome $username"

        burgers.translationX = -900f

        hamburger.setOnClickListener {
            val targetX = if (isMenuOpen) -900f else 0f
            ObjectAnimator.ofFloat(burgers, "translationX", targetX).setDuration(100).start()
            isMenuOpen = !isMenuOpen
        }

        profileSection.setOnClickListener {
            val intent = Intent(this, User::class.java)
            startActivity(intent)
        }

        logoutSection.setOnClickListener {
            logoutUser()
        }

        newsButton.setOnClickListener {
            val intent = Intent(this, Homepage::class.java)
            startActivity(intent)
        }

        aboutSection.setOnClickListener {
            val intent = Intent(this, About::class.java)
            startActivity(intent)
        }

        projectSection.setOnClickListener {
            val intent = Intent(this, CreateProject::class.java)
            startActivity(intent)
        }

        userpost.setOnClickListener {
            val intent = Intent(this, UserPost::class.java)
            startActivity(intent)
        }

    }

    private fun logoutUser() {
        val editor = sharedPreferences.edit()
        editor.clear()
        editor.apply()

        val intent = Intent(this, MainActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        finish()
    }
}