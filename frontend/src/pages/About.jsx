import { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";

const About = () => {

  const [openFaq, setOpenFaq] = useState(null);
  const location = useLocation();

  const toggleFaq = (index) => {
    if (openFaq === index) {
      setOpenFaq(null);
    } else {
      setOpenFaq(index);
    }
  };

  

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

  const faqs = [
    {
      question: "How is the Sustainable Development Foundation funded?",
      answer: "We are funded primarily through grants, corporate partnerships (CSR), and individual donations. Every contribution transparently supports our grassroots initiatives."
    },
    {
      question: "Can I volunteer if I don't live in a project area?",
      answer: "Absolutely! We offer remote volunteering opportunities in areas like digital marketing, content writing, data analysis, and fundraising. Visit our Contact page to get involved."
    },
    {
      question: "How do you measure the impact of your programs?",
      answer: "We employ rigorous monitoring and evaluation frameworks. We collect baseline data before intervention and track key metrics (e.g., school attendance, crop yield, health markers) regularly."
    },
    {
      question: "Are my donations tax-deductible?",
      answer: "Yes, all donations made to the foundation are eligible for 50% tax exemption under Section 80G of the Income Tax Act."
    }
  ];

  return (
    <div className="bg-bg-color min-h-screen">
      {/* Hero Section */}
      <section className="bg-primary text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 className="text-4xl md:text-5xl font-serif font-bold mb-4">About Us</h1>
          <p className="text-xl max-w-2xl mx-auto text-primary-50">Discover our journey, our vision, and the people behind our mission to empower communities.</p>
        </div>
      </section>

      {/* Who We Are Section */}
      <section id="who-we-are" className="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div className="text-center mb-12">
          <h2 className="text-3xl font-serif text-text-primary mb-4">Who We Are</h2>
          <div className="w-24 h-1 bg-accent mx-auto"></div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
          <div className="bg-white p-8 rounded-xl shadow-sm text-center border border-gray-100 hover:shadow-md transition-shadow">
            <div className="bg-[#F9F6EA] w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
              💡
            </div>
            <h3 className="text-xl font-serif text-text-primary mb-4">Our Vision</h3>
            <p className="text-gray-600">To create a sustainable and equitable world where every community thrives in harmony with nature.</p>
          </div>
          <div className="bg-white p-8 rounded-xl shadow-sm text-center border border-gray-100 hover:shadow-md transition-shadow">
            <div className="bg-[#E9EFE1] w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
              🎯
            </div>
            <h3 className="text-xl font-serif text-text-primary mb-4">Our Mission</h3>
            <p className="text-gray-600">Empowering marginalized communities through integrated development programs and sustainable practices.</p>
          </div>
          <div className="bg-white p-8 rounded-xl shadow-sm text-center border border-gray-100 hover:shadow-md transition-shadow">
            <div className="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
              ❤️
            </div>
            <h3 className="text-xl font-serif text-text-primary mb-4">Core Values</h3>
            <p className="text-gray-600">Integrity, Transparency, Inclusivity, Sustainability, and Community Empowerment.</p>
          </div>
        </div>

        {/* Timeline placeholder */}
        <div className="bg-white p-8 rounded-xl shadow-sm border border-gray-100 mb-16">
          <h3 className="text-2xl font-serif text-text-primary mb-6 text-center">Our Journey (2014—Present)</h3>
          <div className="flex flex-col md:flex-row justify-between items-center text-center gap-6">
             <div className="flex-1">
                 <div className="text-primary text-2xl font-bold mb-2">2014</div>
                 <p className="text-sm text-gray-600">Foundation Established</p>
             </div>
             <div className="hidden md:block w-full h-px bg-gray-200 flex-1"></div>
             <div className="flex-1">
                 <div className="text-primary text-2xl font-bold mb-2">2018</div>
                 <p className="text-sm text-gray-600">Expanded to 5 States</p>
             </div>
             <div className="hidden md:block w-full h-px bg-gray-200 flex-1"></div>
             <div className="flex-1">
                 <div className="text-primary text-2xl font-bold mb-2">2022</div>
                 <p className="text-sm text-gray-600">1 Million Beneficiaries</p>
             </div>
             <div className="hidden md:block w-full h-px bg-gray-200 flex-1"></div>
             <div className="flex-1">
                 <div className="text-primary text-2xl font-bold mb-2">2026</div>
                 <p className="text-sm text-gray-600">Global Recognition</p>
             </div>
          </div>
        </div>
      </section>

      {/* Leadership Section */}
      <section id="leadership" className="bg-[#F3EFE4] py-16 scroll-mt-24">
         <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-serif text-text-primary mb-4">Leadership & Governance</h2>
              <div className="w-24 h-1 bg-primary mx-auto"></div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
              {[
                { title: "Founder's Message", icon: "🏆", desc: "A visionary leadership guiding our mission forward." },
                { title: "Board of Trustees", icon: "👥", desc: "Distinguished experts ensuring accountability." },
                { title: "Management Team", icon: "🛡️", desc: "Dedicated professionals managing operations." },
                { title: "Advisory Committee", icon: "🤝", desc: "Strategic counselors providing domain expertise." }
              ].map((item, idx) => (
                <div key={idx} className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                   <div className="flex justify-center mb-4">
                     <div className="w-16 h-16 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-3xl shadow-sm">
                       {item.icon}
                     </div>
                   </div>
                   <h3 className="font-serif text-lg font-bold mb-2">{item.title}</h3>
                   <p className="text-sm text-gray-600">{item.desc}</p>
                </div>
              ))}
            </div>
         </div>
      </section>

      {/* Approach & Partners */}
      <section id="approach" className="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
          {/* Approach */}
          <div>
            <h2 className="text-3xl font-serif text-text-primary mb-6">Our Approach</h2>
            <div className="space-y-6">
              <div className="bg-white p-6 rounded-xl shadow-sm border-l-4 border-primary">
                <h3 className="font-bold text-lg mb-2">Theory of Change</h3>
                <p className="text-gray-600 text-sm">A systematic method to map out our long-term goals and the steps required to achieve sustainable impact.</p>
              </div>
              <div className="bg-white p-6 rounded-xl shadow-sm border-l-4 border-secondary">
                <h3 className="font-bold text-lg mb-2">Community-Centric Model</h3>
                <p className="text-gray-600 text-sm">Putting the community at the heart of decision-making to ensure ownership and long-lasting changes.</p>
              </div>
              <div className="bg-white p-6 rounded-xl shadow-sm border-l-4 border-accent">
                <h3 className="font-bold text-lg mb-2">Monitoring, Evaluation & Learning</h3>
                <p className="text-gray-600 text-sm">Rigorous data collection and analysis to measure our impact and continuously improve our interventions.</p>
              </div>
            </div>
          </div>

          {/* Partners */}
          <div id="partners" className="scroll-mt-24 max-h-105">
            <h2 className="text-3xl font-serif text-text-primary mb-6">Partners & Affiliations</h2>
            <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-8 h-full flex flex-col justify-center">
               <p className="text-gray-600 mb-8 text-center">We collaborate with a diverse network of organizations to amplify our impact.</p>
               <div className="grid grid-cols-2 gap-6 text-center">
                  <div className="p-4 bg-gray-50 rounded-lg">
                    <div className="font-bold text-primary mb-1">Corporate</div>
                    <div className="text-sm text-gray-500">CSR Partners</div>
                  </div>
                  <div className="p-4 bg-gray-50 rounded-lg">
                    <div className="font-bold text-primary mb-1">Public</div>
                    <div className="text-sm text-gray-500">NGO & Academic Partners</div>
                  </div>
                  <div className="p-4 bg-gray-50 rounded-lg col-span-2">
                    <div className="font-bold text-primary mb-1">Civil Society</div>
                    <div className="text-sm text-gray-500">NGO & Academic Partners</div>
                  </div>
               </div>
            </div>
          </div>
        </div>
      </section>

      {/* FAQ Section */}
      <section id="fAq" className="py-24 bg-white" >
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-serif text-text-primary mb-4">Frequently Asked Questions</h2>
            <p className="text-xl text-gray-500">Find answers to common questions about our operations and impact.</p>
          </div>

          <div className="space-y-4">
            {faqs.map((faq, index) => (
              <div 
                key={index}
                className={`border rounded-xl overflow-hidden transition-all duration-300 ${openFaq === index ? 'border-primary shadow-md' : 'border-gray-200 hover:border-primary/50'}`}
               >
                <button 
                  className="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none bg-white"
                  onClick={() => toggleFaq(index)}
                >
                  <span className="font-bold text-text-primary pr-8">{faq.question}</span>
                  <span className={`text-xl transition-transform duration-300 ${openFaq === index ? 'rotate-180 text-primary' : 'text-gray-400'}`}>
                    ▼
                  </span>
                </button>
                <div 
                  className={`px-6 overflow-hidden transition-all duration-300 ease-in-out bg-bg-color ${openFaq === index ? 'max-h-48 py-5 opacity-100' : 'max-h-0 py-0 opacity-0'}`}
                 >
                  <p className="text-gray-700">{faq.answer}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

    </div>
  );
};


export default About;