import { useEffect } from "react";
import { useLocation } from "react-router-dom";

const GetInvolved = () => {
  const location = useLocation(); // 2. Define location here

  useEffect(() => {
    if (location.hash) {
      const el = document.querySelector(location.hash);
      if (el) {
        setTimeout(() => {
          el.scrollIntoView({ behavior: "smooth" });
        }, 100);
      }
    }
  }, [location]);

  return (
    <div className="bg-bg-color min-h-screen pb-24">
      <section className="bg-secondary text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 className="text-4xl md:text-5xl font-serif font-bold mb-4">
            Get Involved
          </h1>
          <p className="text-xl max-w-2xl mx-auto">
            Be a part of our movement. There are many ways to contribute your
            time, skills, and passion.
          </p>
        </div>
      </section>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        {/* Volunteer */}
        <section id="volunteer" className="mb-24 scroll-mt-32">
          <div className="flex flex-col md:flex-row gap-12 items-center">
            <div className="w-full md:w-1/2 rounded-2xl overflow-hidden shadow-sm h-96 relative">
              <img
                src="about/vol.png"
                alt="Volunteer"
                className="w-full h-full object-cover"
              />
              <div className="absolute inset-0 bg-linear-to-t from-primary/80 to-transparent flex items-end p-8">
                <h2 className="text-4xl text-white font-serif font-bold tracking-wide">
                  Volunteer With Us
                </h2>
              </div>
            </div>
            <div className="w-full md:w-1/2 space-y-6">
              <span className="text-4xl">🤝</span>
              <p className="text-gray-600 text-lg leading-relaxed">
                Volunteering is the ultimate exercise in democracy. You vote in
                elections once a year, but when you volunteer, you vote every
                day about the kind of community you want to live in. Join our
                grassroots programs and make a tangible difference on the
                ground.
              </p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                <div className="bg-white p-4 rounded border border-gray-100 shadow-sm flex items-center gap-3">
                  <span className="text-2xl">🌱</span>{" "}
                  <span className="font-bold text-text-primary">
                    Field Volunteer
                  </span>
                </div>
                <div className="bg-white p-4 rounded border border-gray-100 shadow-sm flex items-center gap-3">
                  <span className="text-2xl">💻</span>{" "}
                  <span className="font-bold text-text-primary">
                    Remote Skill-Based
                  </span>
                </div>
              </div>
              <a href="/volunteerform">
                <button className="bg-primary hover:bg-[#5a6425] text-white px-8 py-4 rounded-full font-bold shadow-md hover:-translate-y-1 transition-all mt-6 inline-block">
                  Apply to Volunteer
                </button>
              </a>
            </div>
          </div>
        </section>

        {/* Careers */}
        <section id="careers" className="scroll-mt-32">
          <div className="text-center mb-12">
            <span className="text-4xl mb-4 block animate-float">💼</span>
            <h2 className="text-3xl font-serif text-text-primary mb-4">
              Careers
            </h2>
            <p className="text-gray-500 max-w-2xl mx-auto">
              Join our core team of passionate professionals dedicated to
              driving sustainable development across the country.
            </p>
          </div>

          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden max-w-4xl mx-auto">
            <div className="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-50 transition-colors">
              <div>
                <h3 className="text-xl font-bold text-primary mb-1">
                  Project Manager - WASH
                </h3>
                <p className="text-gray-500 text-sm flex items-center gap-2">
                  <span className="text-base">📍</span> Remote / Rajasthan •
                  Full-time
                </p>
              </div>
              <button className="border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-2 rounded-full font-bold transition-colors">
                Apply Now
              </button>
            </div>
            <div className="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-50 transition-colors">
              <div>
                <h3 className="text-xl font-bold text-primary mb-1">
                  Monitoring & Evaluation Officer
                </h3>
                <p className="text-gray-500 text-sm flex items-center gap-2">
                  <span className="text-base">📍</span> Head Office • Full-time
                </p>
              </div>
              <button className="border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-2 rounded-full font-bold transition-colors">
                Apply Now
              </button>
            </div>
            <div className="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-50 transition-colors">
              <div>
                <h3 className="text-xl font-bold text-primary mb-1">
                  Communications Executive
                </h3>
                <p className="text-gray-500 text-sm flex items-center gap-2">
                  <span className="text-base">📍</span> Hybrid • Contract
                </p>
              </div>
              <button className="border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-2 rounded-full font-bold transition-colors">
                Apply Now
              </button>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
};

export default GetInvolved;
