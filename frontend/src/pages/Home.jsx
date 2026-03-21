import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import Herosection from "./HeroSection";
import Testimonials from "./Testimonials";

const PROGRAMS_API_URL = "http://localhost/sdftrust/backend/api/programs.php";
const SUBSCRIBE_API_URL = "http://localhost/sdftrust/backend/api/subscribe.php";
const ADMIN_BASE_URL = "http://localhost/sdftrust/backend/admin/";

const makeImageUrl = (path) => {
  if (!path) return "https://via.placeholder.com/800x500?text=No+Image";

  if (typeof path !== "string") {
    return "https://via.placeholder.com/800x500?text=No+Image";
  }

  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }

  return `${ADMIN_BASE_URL}${path.replace(/^\/+/, "")}`;
};

const createSlug = (text) => {
  if (!text) return "";
  return text
    .toString()
    .toLowerCase()
    .trim()
    .replace(/&/g, "and")
    .replace(/[^\w\s-]/g, "")
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
};

const Home = () => {
  const [email, setEmail] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [message, setMessage] = useState({ text: "", type: "" });

  const [programsList, setProgramsList] = useState([]);
  const [programsLoading, setProgramsLoading] = useState(true);
  const [programsError, setProgramsError] = useState("");

  useEffect(() => {
    const fetchPrograms = async () => {
      try {
        const response = await fetch(PROGRAMS_API_URL);

        if (!response.ok) {
          throw new Error("Failed to fetch programs");
        }

        const data = await response.json();

        const rawPrograms = Array.isArray(data)
          ? data
          : Array.isArray(data.data)
            ? data.data
            : [];

        const normalizedPrograms = rawPrograms
          .slice(0, 4)
          .map((program, index) => ({
            ...program,
            id: program.id || index + 1,
            title: program.title || "Untitled Program",
            description: program.description || "No description available.",
            image_url: makeImageUrl(program.image_url),
            slug:
              program.slug ||
              createSlug(program.title) ||
              `program-${index + 1}`,
          }));

        setProgramsList(normalizedPrograms);
      } catch (error) {
        console.error("Programs fetch error:", error);
        setProgramsError(error.message || "Failed to load programs");
      } finally {
        setProgramsLoading(false);
      }
    };

    fetchPrograms();
  }, []);

  const handleSubscribe = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setMessage({ text: "", type: "" });

    try {
      const response = await fetch(SUBSCRIBE_API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email }),
      });

      const data = await response.json();

      if (data.status === "success") {
        setMessage({
          text: data.message || "Subscribed successfully.",
          type: "success",
        });
        setEmail("");
      } else {
        setMessage({
          text: data.message || "Subscription failed.",
          type: "error",
        });
      }
    } catch (error) {
      console.error("Subscription error:", error);
      setMessage({
        text: "Failed to connect to the server. Please ensure the PHP backend is running on localhost.",
        type: "error",
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div>
      <Herosection />

      <section className="py-10 relative bg-bg-color">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col lg:flex-row gap-12 items-center">
            <div className="lg:w-1/3">
              <div className="flex items-center gap-3 mb-6">
                <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-2xl shadow-sm animate-float">
                  🌱
                </div>
                <h2 className="text-3xl md:text-4xl font-serif text-text-primary leading-tight">
                  About Our
                  <br />
                  Organization
                </h2>
              </div>

              <p className="text-gray-600 mb-6 leading-relaxed">
                Duis eterueh voie roiar ma inots elitrum velite ne cuolestieru
                cuigiatat llvoneaglut excepteur cillum dolore eua protert it.
                augait noes parrat teilisuntials idupiliees ipum.
              </p>

              <ul className="space-y-3 mb-8">
                <li className="flex items-start">
                  <span className="w-1.5 h-1.5 rounded-full bg-primary mt-2 mr-3 shrink-0"></span>
                  <span className="text-gray-600 text-sm">
                    Pesentector tappelpat, euell cocenata velf, colotiut nnos
                  </span>
                </li>
                <li className="flex items-start">
                  <span className="w-1.5 h-1.5 rounded-full bg-primary mt-2 mr-3 shrink-0"></span>
                  <span className="text-gray-600 text-sm">
                    Uit fanlis sed dolem frigiats mulit zooflaits veilles
                  </span>
                </li>
              </ul>

              <Link to="/about">
                <button className="bg-primary hover:bg-[#5a6425] text-white px-8 py-2.5 rounded-full font-medium transition-colors">
                  View More
                </button>
              </Link>
            </div>

            <div className="lg:w-2/3 grid grid-cols-1 md:grid-cols-3 gap-6">
              {[
                {
                  title: "Water Conservation",
                  info: "Lorem ipsum dolor sit amet ociae idu ailsing elit, sed dini net gamtz.",
                  img: "about/2.jpg",
                },
                {
                  title: "Sustainable Agriculture",
                  info: "Lorem ipsum dolor sit amet ociae idu ailsing elit, sed dini net gamtz.",
                  img: "about/2.jpg",
                },
                {
                  title: "Community Development",
                  info: "Lorem ipsum dolor sit amet ociae idu ailsing elit, sed dini net gamtz.",
                  img: "about/2.jpg",
                },
              ].map((card, idx) => (
                <div
                  key={idx}
                  className="bg-white rounded-2xl shadow-sm text-center border border-gray-100 pb-6 flex flex-col h-full hover:shadow-md transition-shadow"
                >
                  <div className="p-4">
                    <img
                      src={card.img}
                      alt={card.title}
                      className="w-full h-32 object-cover rounded-xl shadow-sm"
                      onError={(e) => {
                        e.currentTarget.src =
                          "https://via.placeholder.com/500x300?text=Image+Not+Found";
                      }}
                    />
                  </div>

                  <div className="p-5 grow flex flex-col">
                    <h3 className="text-xl font-serif text-text-primary mb-3">
                      {card.title}
                    </h3>
                    <p className="text-gray-500 text-sm mb-6 grow">
                      {card.info}
                    </p>
                    <button className="bg-primary hover:bg-[#5a6425] text-white px-6 py-2 rounded-full font-medium text-sm transition-colors self-start mt-auto">
                      Learn More
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="py-10 bg-[#F3EFE4] relative">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl font-serif text-text-primary mb-12">
            Our Focus Areas
          </h2>

          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
            <div className="bg-white p-6 rounded-xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 flex items-center gap-4 justify-center border border-gray-100">
              <div className="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-3xl shadow-sm animate-float">
                👥
              </div>
              <div className="text-left">
                <p className="text-2xl font-bold text-text-primary">40+</p>
                <p className="text-xs text-gray-500 uppercase tracking-wide">
                  Communities Empowered
                </p>
              </div>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 flex items-center gap-4 justify-center border border-gray-100">
              <div className="w-14 h-14 rounded-full bg-cyan-50 flex items-center justify-center text-3xl shadow-sm animate-float-delayed">
                💧
              </div>
              <div className="text-left">
                <p className="text-2xl font-bold text-text-primary">200+</p>
                <p className="text-xs text-gray-500 uppercase tracking-wide">
                  Water Resources Restored
                </p>
              </div>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 flex items-center gap-4 justify-center border border-gray-100">
              <div className="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-3xl shadow-sm animate-float">
                🧑‍🌾
              </div>
              <div className="text-left">
                <p className="text-2xl font-bold text-text-primary">1,500+</p>
                <p className="text-xs text-gray-500 uppercase tracking-wide">
                  Farmers Trained
                </p>
              </div>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 flex items-center gap-4 justify-center border border-gray-100">
              <div className="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center text-3xl shadow-sm animate-float-delayed">
                🌳
              </div>
              <div className="text-left">
                <p className="text-2xl font-bold text-text-primary">25,000+</p>
                <p className="text-xs text-gray-500 uppercase tracking-wide">
                  Trees Planted
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="py-10 bg-bg-color">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl font-serif text-text-primary mb-12">
            Our Programs
          </h2>

          {programsLoading ? (
            <p className="text-primary font-semibold">Loading programs...</p>
          ) : programsError ? (
            <p className="text-red-500 font-semibold">{programsError}</p>
          ) : programsList.length === 0 ? (
            <p className="text-gray-500">No programs found.</p>
          ) : (
            <>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {programsList.map((program) => (
                  <div
                    key={program.id}
                    className="bg-white rounded-xl border border-gray-100 text-left hover:shadow-lg transition-shadow overflow-hidden flex flex-col h-full"
                  >
                    <div className="h-48 overflow-hidden relative">
                      <img
                        src={program.image_url}
                        alt={program.title}
                        className="w-full h-full object-cover"
                        onError={(e) => {
                          e.currentTarget.src =
                            "https://via.placeholder.com/800x500?text=Image+Not+Found";
                        }}
                      />
                    </div>

                    <div className="p-6 grow flex flex-col">
                      <h3 className="text-xl font-serif font-bold text-text-primary mb-3 leading-tight">
                        {program.title}
                      </h3>

                      <p className="text-gray-500 text-sm mb-6 grow">
                        {program.description.length > 100
                          ? `${program.description.slice(0, 100)}...`
                          : program.description}
                      </p>

                      <Link
                        to={`/programdetails/${program.slug}`}
                        className="bg-primary hover:bg-[#5a6425] text-white px-6 py-2 rounded-full font-medium text-sm transition-colors self-start mt-auto inline-block"
                      >
                        Learn More
                      </Link>
                    </div>
                  </div>
                ))}
              </div>

              <div className="mt-10">
                <Link
                  to="/programs"
                  className="inline-block border border-primary text-primary hover:bg-primary hover:text-white px-8 py-3 rounded-full font-semibold transition-colors"
                >
                  View All Programs
                </Link>
              </div>
            </>
          )}
        </div>
      </section>

      <Testimonials />

      <section className="py-10 bg-[#E9EFE1] relative">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10 relative">
          <h2 className="text-3xl font-serif text-text-primary mb-4">
            Get Involved
          </h2>
          <p className="text-xl text-gray-700 mb-12">
            Join Us in Making a Difference
          </p>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <a href="/volunteerform">
              <div className="bg-white rounded-2xl overflow-hidden shadow-sm relative group cursor-pointer h-80">
                <img
                  src="about/vol.png"
                  alt="Volunteer"
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  onError={(e) => {
                    e.currentTarget.src =
                      "https://via.placeholder.com/800x500?text=Volunteer";
                  }}
                />
                <div className="absolute inset-0 bg-black/40 flex items-center justify-center">
                  <h3 className="text-3xl text-white font-serif font-bold tracking-wide">
                    Volunteer With Us
                  </h3>
                </div>
              </div>
            </a>

            <a href="/donate">
              <div className="bg-white rounded-2xl overflow-hidden shadow-sm relative group cursor-pointer h-80">
                <img
                  src="banner/donate-page.png"
                  alt="Donate"
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  onError={(e) => {
                    e.currentTarget.src =
                      "https://via.placeholder.com/800x500?text=Donate";
                  }}
                />
                <div className="absolute inset-0 bg-primary/40 flex items-center justify-center">
                  <h3 className="text-3xl text-white font-serif font-bold tracking-wide">
                    Make a Donation
                  </h3>
                </div>
              </div>
            </a>
          </div>
        </div>
      </section>

      <section className="py-10 bg-primary/10 border-t border-primary/20">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <span className="text-4xl mb-4 block animate-float">✉️</span>
          <h2 className="text-3xl font-serif text-text-primary mb-4">
            Subscribe to Our Newsletter
          </h2>
          <p className="text-gray-600 mb-8 max-w-2xl mx-auto">
            Stay updated with our latest projects, success stories, and ways you
            can help. Join our community of changemakers today.
          </p>

          <form
            onSubmit={handleSubscribe}
            className="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto"
          >
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="Enter your email address"
              className="grow px-6 py-4 rounded-full border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white shadow-sm"
              required
              disabled={isSubmitting}
            />
            <button
              type="submit"
              disabled={isSubmitting}
              className="bg-primary hover:bg-[#5a6425] text-white px-8 py-4 rounded-full font-bold transition-all shadow-md hover:-translate-y-1 hover:shadow-lg whitespace-nowrap disabled:opacity-70 disabled:hover:translate-y-0"
            >
              {isSubmitting ? "Subscribing..." : "Subscribe"}
            </button>
          </form>

          {message.text && (
            <div
              className={`max-w-lg mx-auto mt-4 p-3 rounded-lg text-sm font-medium ${
                message.type === "success"
                  ? "bg-green-100 text-green-800 border border-green-200"
                  : "bg-red-100 text-red-800 border border-red-200"
              }`}
            >
              {message.text}
            </div>
          )}
        </div>
      </section>
    </div>
  );
};

export default Home;
