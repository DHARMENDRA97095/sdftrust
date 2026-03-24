import { useEffect, useMemo, useRef, useState } from "react";
import QRCode from "react-qr-code";

export default function DonationForm() {
  const initialFormData = {
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    donation_amount: "",
    address: "",
    message: "",
    wants_80g: false,
    pan_number: "",
  };

  const PAYMENT_TIME_LIMIT = 60; // 60 seconds
  const RESET_DELAY = 3000; // 3 seconds

  const [showPan, setShowPan] = useState(false);
  const [formData, setFormData] = useState(initialFormData);
  const [responseMsg, setResponseMsg] = useState("");
  const [savedData, setSavedData] = useState(null);
  const [showPayment, setShowPayment] = useState(false);
  const [paymentSuccess, setPaymentSuccess] = useState(false);
  const [transactionId, setTransactionId] = useState("");
  const [timeLeft, setTimeLeft] = useState(PAYMENT_TIME_LIMIT);
  const [isCreatingDonation, setIsCreatingDonation] = useState(false);
  const [isCheckingStatus, setIsCheckingStatus] = useState(false);

  const timerRef = useRef(null);
  const pollRef = useRef(null);
  const resetRef = useRef(null);

  const upiId = "9709544166@ybl";
  const payeeName = "SDF Trust";
  const transactionNote = "Donation for children support";

  const CREATE_DONATION_API =
    "http://localhost/backend/api/create-donation.php";
  const CHECK_STATUS_API =
    "http://localhost/backend/api/check-payment-status.php";
  const EXPIRE_PAYMENT_API =
    "http://localhost/backend/api/expire-payment.php";

  const createUpiUrl = ({ upiId, payeeName, amount, note, transactionId }) => {
    const params = new URLSearchParams({
      pa: upiId,
      pn: payeeName,
      am: String(amount),
      cu: "INR",
      tn: `${note} ${transactionId}`,
    });

    return `upi://pay?${params.toString()}`;
  };

  const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  const validatePhone = (phone) => /^[6-9]\d{9}$/.test(phone);
  const validatePAN = (pan) => /^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan);

  const clearAllTimers = () => {
    if (timerRef.current) clearInterval(timerRef.current);
    if (pollRef.current) clearInterval(pollRef.current);
    if (resetRef.current) clearTimeout(resetRef.current);
  };

  const resetToForm = () => {
    clearAllTimers();
    setFormData(initialFormData);
    setSavedData(null);
    setShowPayment(false);
    setShowPan(false);
    setPaymentSuccess(false);
    setTransactionId("");
    setTimeLeft(PAYMENT_TIME_LIMIT);
    setIsCreatingDonation(false);
    setIsCheckingStatus(false);
    setResponseMsg("");
  };

  // Payment screen close karega, but form data preserve rahega
  const backToFormOnly = async () => {
    clearAllTimers();

    if (transactionId && !paymentSuccess) {
      await markExpired(transactionId);
    }

    setSavedData(null);
    setShowPayment(false);
    setPaymentSuccess(false);
    setTransactionId("");
    setTimeLeft(PAYMENT_TIME_LIMIT);
    setIsCreatingDonation(false);
    setIsCheckingStatus(false);
    setResponseMsg("");
  };

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;

    setFormData((prev) => ({
      ...prev,
      [name]:
        type === "checkbox"
          ? checked
          : name === "pan_number"
            ? value.toUpperCase()
            : value,
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

  const markExpired = async (txnId) => {
    try {
      await fetch(EXPIRE_PAYMENT_API, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ transaction_id: txnId }),
      });
    } catch (error) {
      console.error("Expire API error:", error);
    }
  };

  const checkPaymentStatus = async (txnId) => {
    if (!txnId) return;

    try {
      setIsCheckingStatus(true);

      const res = await fetch(
        `${CHECK_STATUS_API}?transaction_id=${encodeURIComponent(txnId)}`
      );
      const data = await res.json();

      if (!data.success) return;

      if (data.payment_status === "success") {
        clearAllTimers();
        setPaymentSuccess(true);
        setResponseMsg("Successful payment");

        resetRef.current = setTimeout(() => {
          resetToForm();
        }, RESET_DELAY);
      }

      if (data.payment_status === "expired") {
        clearAllTimers();
        setResponseMsg("QR expired. Please try again.");

        resetRef.current = setTimeout(() => {
          resetToForm();
        }, 3000);
      }
    } catch (error) {
      console.error("Status check error:", error);
    } finally {
      setIsCheckingStatus(false);
    }
  };

  const startCountdownAndPolling = (txnId) => {
    setTimeLeft(PAYMENT_TIME_LIMIT);

    timerRef.current = setInterval(() => {
      setTimeLeft((prev) => {
        if (prev <= 1) {
          clearInterval(timerRef.current);
          clearInterval(pollRef.current);
          markExpired(txnId);
          setResponseMsg("QR expired. Please try again.");

          resetRef.current = setTimeout(() => {
            resetToForm();
          }, 3000);

          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    pollRef.current = setInterval(() => {
      checkPaymentStatus(txnId);
    }, 3000);
  };

  const handleSaveInformation = async (e) => {
    e.preventDefault();
    setResponseMsg("");

    if (!validateEmail(formData.email)) {
      setResponseMsg("Please enter a valid email address.");
      return;
    }

    if (!validatePhone(formData.phone)) {
      setResponseMsg("Please enter a valid 10-digit Indian phone number.");
      return;
    }

    if (!formData.donation_amount || Number(formData.donation_amount) <= 0) {
      setResponseMsg("Please enter a valid donation amount.");
      return;
    }

    if (formData.wants_80g && !validatePAN(formData.pan_number)) {
      setResponseMsg("Please enter a valid PAN number.");
      return;
    }

    try {
      setIsCreatingDonation(true);

      const res = await fetch(CREATE_DONATION_API, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      const data = await res.json();

      if (data.success) {
        setSavedData(formData);
        setTransactionId(data.transaction_id);
        setShowPayment(true);
        setPaymentSuccess(false);
        startCountdownAndPolling(data.transaction_id);
      } else {
        setResponseMsg(data.message || "Unable to start payment.");
      }
    } catch (error) {
      setResponseMsg("Something went wrong while starting payment.");
      console.error(error);
    } finally {
      setIsCreatingDonation(false);
    }
  };

  useEffect(() => {
    return () => {
      clearAllTimers();
    };
  }, []);

  const upiUrl = useMemo(() => {
    if (!savedData?.donation_amount || !transactionId) return "";

    return createUpiUrl({
      upiId,
      payeeName,
      amount: savedData.donation_amount,
      note: transactionNote,
      transactionId,
    });
  }, [savedData, transactionId]);

  const minutes = String(Math.floor(timeLeft / 60)).padStart(2, "0");
  const seconds = String(timeLeft % 60).padStart(2, "0");

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
        <div
          className={`mb-4 rounded-lg px-4 py-3 text-sm font-medium ${
            paymentSuccess
              ? "bg-green-100 text-green-700 border border-green-300"
              : "bg-gray-100 text-gray-700 border border-gray-200"
          }`}
        >
          <p>{responseMsg}</p>
          {transactionId && paymentSuccess && (
            <p className="mt-1 font-semibold">Transaction ID: {transactionId}</p>
          )}
        </div>
      )}

      {!showPayment ? (
        <form className="space-y-4" onSubmit={handleSaveInformation}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
              type="text"
              name="first_name"
              placeholder="First Name"
              className="w-full border rounded-lg px-4 py-3"
              value={formData.first_name}
              onChange={handleChange}
              required
            />
            <input
              type="text"
              name="last_name"
              placeholder="Last Name"
              className="w-full border rounded-lg px-4 py-3"
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
              className="w-full border rounded-lg px-4 py-3"
              value={formData.email}
              onChange={handleChange}
              required
            />
            <input
              type="text"
              name="phone"
              placeholder="Your Number"
              className="w-full border rounded-lg px-4 py-3"
              value={formData.phone}
              onChange={handleChange}
              required
            />
          </div>

          <input
            type="number"
            name="donation_amount"
            placeholder="Donation Amount"
            className="w-full border rounded-lg px-4 py-3"
            value={formData.donation_amount}
            onChange={handleChange}
            required
            min="1"
          />

          <div className="flex flex-wrap gap-3">
            {[100, 500, 1000, 2000].map((amount) => (
              <button
                key={amount}
                type="button"
                onClick={() =>
                  setFormData((prev) => ({
                    ...prev,
                    donation_amount: String(amount),
                  }))
                }
                className="px-4 py-2 rounded-full border border-yellow-400 bg-yellow-50 hover:bg-yellow-100 text-sm font-medium"
              >
                ₹{amount}
              </button>
            ))}
          </div>

          <input
            type="text"
            name="address"
            placeholder="Your Address"
            className="w-full border rounded-lg px-4 py-3"
            value={formData.address}
            onChange={handleChange}
            required
          />

          <textarea
            name="message"
            placeholder="Your message..."
            className="w-full border rounded-lg px-4 py-3 h-32 resize-none"
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
              className="w-full border rounded-lg px-4 py-3"
              value={formData.pan_number}
              onChange={handleChange}
              required
            />
          )}

          <button
            type="submit"
            disabled={isCreatingDonation}
            className="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-3 rounded-full transition disabled:opacity-50"
          >
            {isCreatingDonation ? "Please wait..." : "Donate Now →"}
          </button>
        </form>
      ) : (
        <div className="text-center space-y-6">
          <h3 className="text-xl font-bold">Complete Your Donation Payment</h3>

          {!paymentSuccess && (
            <div className="mx-auto max-w-md rounded-full bg-red-50 border border-red-200 px-4 py-2 text-red-700 font-semibold">
              Time Left: {minutes}:{seconds}
            </div>
          )}

          <div className="relative bg-gray-50 border rounded-xl p-6 max-w-md mx-auto">
            {!paymentSuccess && (
              <button
                type="button"
                onClick={backToFormOnly}
                className="absolute top-3 right-3 w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 font-bold text-lg flex items-center justify-center"
                aria-label="Close payment"
                title="Close payment"
              >
                ×
              </button>
            )}

            <p className="text-lg font-semibold mb-2">
              Donation Amount: ₹{savedData?.donation_amount}
            </p>

            {!paymentSuccess && (
              <>
                <p className="text-sm text-gray-500 mb-4">
                  Scan this QR code using any UPI app
                </p>

                {upiUrl && (
                  <div className="bg-white p-4 inline-block rounded-xl border shadow-sm">
                    <QRCode value={upiUrl} size={220} />
                  </div>
                )}

                <div className="mt-4 text-sm text-gray-600 break-all space-y-1">
                  <p>
                    <span className="font-semibold">UPI ID:</span> {upiId}
                  </p>
                  <p>
                    <span className="font-semibold">Transaction ID:</span>{" "}
                    {transactionId}
                  </p>
                </div>

                {upiUrl && (
                  <a
                    href={upiUrl}
                    className="mt-4 inline-block px-6 py-3 rounded-full bg-green-600 text-white hover:bg-green-700 transition"
                  >
                    Pay with UPI App
                  </a>
                )}

                <div className="mt-4">
                  <button
                    type="button"
                    onClick={backToFormOnly}
                    className="px-6 py-2 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 transition"
                  >
                    Cancel Payment
                  </button>
                </div>

                <p className="mt-4 text-xs text-gray-500">
                  Payment status is being checked automatically...
                </p>

                {isCheckingStatus && (
                  <p className="mt-2 text-xs text-blue-600">
                    Checking payment status...
                  </p>
                )}
              </>
            )}

            {paymentSuccess && (
              <div className="py-6">
                <p className="text-2xl font-bold text-green-600">
                  Successful payment
                </p>
                <p className="mt-2 text-sm text-gray-600">
                  Redirecting to donation form...
                </p>
                <p className="mt-2 font-semibold">
                  Transaction ID: {transactionId}
                </p>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}