package com.example.hopebridge

import android.content.Intent
import android.content.SharedPreferences
import android.os.Bundle
import android.util.Log
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import java.io.File

class User : AppCompatActivity() {


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_user)

        val backbutton: ImageView = findViewById(R.id.back)

        val sharedPreferences = getSharedPreferences("userPrefs", MODE_PRIVATE)

        val username = sharedPreferences.getString("username", "Guest") ?: "Guest"
        val email = sharedPreferences.getString("email", "Guest") ?: "Guest"

        findViewById<TextView>(R.id.username).text = "$username"

        findViewById<TextView>(R.id.email).text = "$email"


        backbutton.setOnClickListener {
            val intent = Intent(this, Homepage::class.java)
            startActivity(intent)
        }

    }
}
