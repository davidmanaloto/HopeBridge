package com.example.hopebridge

import android.content.Intent
import android.os.Bundle
import android.widget.ImageView
import android.widget.TextView
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity

class UserInfo : AppCompatActivity() {


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
