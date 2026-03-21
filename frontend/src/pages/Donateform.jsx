import { useState } from "react";

export default function DonationForm() {
  const [showPan, setShowPan] = useState(false);

  return (
    <div className="max-w-6xl mx-auto mt-8 p-6 bg-white rounded-xl shadow-md">
      
      {/* Heading */}
      <h2 className="text-2xl font-bold mb-2">
        Empower Children Through Music, Education, And Dignity
      </h2>
      <p className="text-gray-500 mb-6">
        Empowering underprivileged children through music, education, and cultural care.
      </p>

      {/* Form */}
      <form className="space-y-4">

        {/* Name */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input
            type="text"
            placeholder="First Name"
            className="input"
            required
          />
          <input
            type="text"
            placeholder="Last Name"
            className="input"
            required
          />
        </div>

        {/* Email + Phone */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input
            type="email"
            placeholder="Your Email"
            className="input"
            required
          />
          <input
            type="text"
            placeholder="Your Number"
            className="input"
            required
          />
        </div>

        {/* Donation */}
        <input
          type="text"
          placeholder="Donation"
          className="input"
          required
        />

        {/* Address */}
        <input
          type="text"
          placeholder="Your Address"
          className="input"
          required
        />

        {/* Message */}
        <textarea
          placeholder="Your message..."
          className="input h-32 resize-none"
        ></textarea>

        {/* Checkbox */}
        <div className="flex items-center gap-2">
          <input
            type="checkbox"
            onChange={(e) => setShowPan(e.target.checked)}
          />
          <label>Check here if you want 80G Tax Redemption</label>
        </div>

        {/* PAN Field */}
        {showPan && (
          <input
            type="text"
            placeholder="Your PAN Number"
            maxLength={10}
            className="input"
            required
          />
        )}

        {/* Submit Button */}
        <button
          type="submit"
          className="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-3 rounded-full transition"
        >
          Save Informations →
        </button>
      </form>
    </div>
  );
}