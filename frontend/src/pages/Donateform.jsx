import { useState } from "react";

export default function DonationForm() {
  const [showPan, setShowPan] = useState(false);
  const [formData, setFormData] = useState({
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    donation_amount: "",
    address: "",
    message: "",
    wants_80g: false,
    pan_number: "",
  });

  const [responseMsg, setResponseMsg] = useState("");

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;

    setFormData((prev) => ({
      ...prev,
      [name]: type === "checkbox" ? checked : value,
    }));

    if (name === "wants_80g") {
      setShowPan(checked);
      if (!checked) {
        setFormData((prev) => ({
          ...prev,
          wants_80g: false,
          pan_number: "",
        }));
      }
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setResponseMsg("");

    try {
      const res = await fetch(
        "http://localhost/sdftrust/backend/api/donation.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(formData),
        },
      );

      const data = await res.json();

      if (data.success) {
        setResponseMsg(data.message);
        setFormData({
          first_name: "",
          last_name: "",
          email: "",
          phone: "",
          donation_amount: "",
          address: "",
          message: "",
          wants_80g: false,
          pan_number: "",
        });
        setShowPan(false);
      } else {
        setResponseMsg(data.message);
      }
    } catch (error) {
      setResponseMsg("Something went wrong while submitting the form.");
    }
  };

  return (
    <div className="max-w-6xl mx-auto mt-8 p-6 bg-white rounded-xl shadow-md">
      <h2 className="text-2xl font-bold mb-2">
        Empower Children Through Music, Education, And Dignity
      </h2>
      <p className="text-gray-500 mb-6">
        Empowering underprivileged children through music, education, and
        cultural care.
      </p>

      {responseMsg && (
        <div className="mb-4 rounded-lg bg-gray-100 px-4 py-3 text-sm">
          {responseMsg}
        </div>
      )}

      <form className="space-y-4" onSubmit={handleSubmit}>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input
            type="text"
            name="first_name"
            placeholder="First Name"
            className="input"
            value={formData.first_name}
            onChange={handleChange}
            required
          />
          <input
            type="text"
            name="last_name"
            placeholder="Last Name"
            className="input"
            value={formData.last_name}
            onChange={handleChange}
            required
          />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input
            type="email"
            name="email"
            placeholder="Your Email"
            className="input"
            value={formData.email}
            onChange={handleChange}
            required
          />
          <input
            type="text"
            name="phone"
            placeholder="Your Number"
            className="input"
            value={formData.phone}
            onChange={handleChange}
            required
          />
        </div>

        <input
          type="number"
          name="donation_amount"
          placeholder="Donation"
          className="input"
          value={formData.donation_amount}
          onChange={handleChange}
          required
        />

        <input
          type="text"
          name="address"
          placeholder="Your Address"
          className="input"
          value={formData.address}
          onChange={handleChange}
          required
        />

        <textarea
          name="message"
          placeholder="Your message..."
          className="input h-32 resize-none"
          value={formData.message}
          onChange={handleChange}
        ></textarea>

        <div className="flex items-center gap-2">
          <input
            type="checkbox"
            name="wants_80g"
            checked={formData.wants_80g}
            onChange={handleChange}
          />
          <label>Check here if you want 80G Tax Redemption</label>
        </div>

        {showPan && (
          <input
            type="text"
            name="pan_number"
            placeholder="Your PAN Number"
            maxLength={10}
            className="input"
            value={formData.pan_number}
            onChange={handleChange}
            required
          />
        )}

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
