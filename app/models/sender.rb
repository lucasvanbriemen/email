class Sender < ApplicationRecord
  has_many :emails

  SENDER_NAME_FALLBACKS = [ nil, "", "0", " " ].freeze

  def image_url
    "https://img.logo.dev/#{top_level_domain}?token=pk_YHpEPFuOTnGDZ6nmBhgIog&retina=true"
  end

  def top_level_domain
    domain = email.split("@").last.to_s.downcase
    # Use the Public Suffix List so multi-part TLDs (co.uk, com.au, ...) work.
    PublicSuffix.domain(domain, ignore_private: true) || domain
  end

  def name
    # If the DB name is 0 or blank, fall back to the email prefix.
    db_name = super
    if SENDER_NAME_FALLBACKS.exclude?(db_name)
      db_name
    else
      email.split("@").first
    end
  end
end
