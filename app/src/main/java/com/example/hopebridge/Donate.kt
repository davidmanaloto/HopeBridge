package com.example.hopebridge

import android.app.Activity
import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity

class DonateActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_donate)

        val donateAmountEditText = findViewById<EditText>(R.id.donateAmountEditText)
        val donateButton = findViewById<Button>(R.id.donateButton)

        donateButton.setOnClickListener {
            val donationAmountText = donateAmountEditText.text.toString()

            if (donationAmountText.isNotEmpty()) {
                val donationAmount = donationAmountText.toDouble()

                val resultIntent = Intent()
                resultIntent.putExtra("donationAmount", donationAmount)
                setResult(Activity.RESULT_OK, resultIntent)
                finish() // Close this activity and return to CreateProject
            } else {
                Toast.makeText(this, "Please enter a valid amount", Toast.LENGTH_SHORT).show()
            }
        }
    }
}
